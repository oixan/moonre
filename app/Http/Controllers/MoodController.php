<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

use App\Mail\UserVerification;
use App\Mood;
use App\User;
use App\Report;
use JWTAuth;
use App\Category;

class MoodController extends Controller
{

    public function __construct(){
        $this->middleware('jwt.auth',[ 'only' => [
            'store', 'update', 'destroy'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$moods = Mood::all();

        // $moods = Mood::skip(0)->take(14)->withCount('likes')->get();

        // $moods = Mood::withCount('likes')->get();

        // da commentare usare indexSkip
        $user = null;
        $userId = "0";
        try{
            $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null );
            $userId = ( $user ? $user->id : "0" );
        }catch(TokenExpiredException $e){    }

        $moods = Mood::leftJoin(
                            DB::raw("( select *
                                       from user_like_mood 
                                       where user_id = '" . $userId . "' )  user_like_mood"), 'moods.id', '=', 'user_like_mood.mood_id')
                        ->select('user_like_mood.user_id as liked', 'moods.*')
                        ->withCount('likes')
                        ->orderBy('created_at', 'asc')
                        ->get();

        $user = User::find(4);                
        Mail::to('oixan@live.it')->send(new UserVerification($user)); // test - delete this row

        return  response()->json([
            'moodsList' => $moods,
            'user' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $maxMoodForDay = 4;

        $request->validate([
            'message' => 'required|min:10|max:220',
            'message2' => 'string|max:220|nullable',
            'category_id' => 'exists:category,id|nullable'
        ]);
        
        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }
        
        $moodUserCount = DB::table('moods')
                    ->where('user_id', $user->id)
                    ->whereRaw('DATE(created_at) = CURDATE()')
                    ->count();

        if ( $maxMoodForDay - $moodUserCount <= 0 ){
            return response()->json([ 
                'message'=>'Max moods for day reached',
                'maxMoodToday' => $maxMoodForDay
            ], 403);
        }
        
        $guid = Uuid::generate();
        $mood = new Mood([
            'id'=> $guid,
            'message'=>$request->input('message'),
            'message2'=>$request->input('message2'),
            'category_id' => $request->input('category_id')
        ]);

        try{
            $user->moods()->save($mood);
        }catch(Exception $e){
            $response = [
                'message'=>'An Error Occured!'
            ];
        }

        $mood->id = utf8_encode($mood->id);
        $response = [
            'message'=>'Mood created!',
            'mood' => $mood,
            'moodToday' => ++$moodUserCount,
            'maxMoodToday' => $maxMoodForDay
        ];

        return response()->json($response); // JSON($response,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mood = Mood::select('moods.*')
            ->withCount('likes')
            ->where('id', $id)
            ->get()->first();

        if ( !$mood )
            return $response = [
                    'message' => "ID not found"
                ];    

        $liked = null;
        $user = null;
        try{
            if ( $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null ) ){
                    $likedTemp =    DB::table('user_like_mood')
                                    ->where('mood_id', '=', $mood->id)
                                    ->where('user_id', '=', $user->id)
                                    ->get();
                    
                    if ( $likedTemp && count($likedTemp) > 0 )
                        $liked = $user->id;
            }
        }catch(TokenExpiredException $e){

        }

        $mood->liked = $liked;

        if ( $mood ){

            $reports = Report::all();
            $categories = Category::all();

            $mood->id = utf8_encode($mood->id);

            $response = [
                'message' => 'Element found!',
                'mood' => $mood,
                'reports'=> $reports,
                'categories' => $categories,
            ];

            return response()->json($response,200); 
        }

        $response = [
            'message' => 'Element not found!'
        ];

        return response()->json($response,404); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|min:10|max:220',
            'message2' => 'string|max:220|nullable',
            'category_id' => 'exists:category,id|nullable'
        ]);
        
        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }

        $mood = Mood::find($id);

        if ( $mood )           
            if ( $mood->user_id == $user->id){
                try{
                    $mood->message = $request->input('message');
                    $mood->message2 = $request->input('message2');
                    $mood->category_id = $request->input('category_id');
                    $mood->save();
                }catch(Exception $e){
                    $response = [
                        'message'=>'An Error Occured!'
                    ];
                    return response()->json($response,401); // JSON($response,201);
                }
        
                $mood->id = utf8_encode($mood->id);
                $response = [
                    'message'=>'Mood update!',
                    'mood' => $mood
                ];
                return response()->json($response,200); // JSON($response,201);
            }else{
                $response = [
                    'message'=>'Operation not allowed!'
                ];

                return response()->json($response,401); // JSON($response,201);
            }

        $response = [
            'message'=>'Mood not found!'
        ];

        return response()->json($response,404); // JSON($response,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }
        
        if ( $mood = Mood::find($id) ){
            if ( $mood->user_id == $user->id){
                if ( $mood->delete() ){
                    $mood->id = utf8_encode($mood->id);
                    $response = [
                        'message' => 'Element deleted!',
                        'mood' => $mood
                    ];
                    return response()->json($response,200); 
                }else{
                    $response = [
                        'message' => 'Error delete element!'
                    ];
                    return response()->json($response,404); 
                }
            }else{
                $response = [
                    'message' => 'Error delete element for this user!'
                ];
                return response()->json($response,404); 
            }
        }

        $response = [
            'message' => 'Element not found!'
        ];
        
        return response()->json($response,404); 
    }

    public function getMoodsSuggest(){

        $userId = "0";

        try{
            $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null );
            $userId = ( $user ? $user->id : "0" );
        }catch(TokenExpiredException $e){    }

        $moodsSuggest =  Mood::select('user_like_mood.user_id as liked', 'moods.*')
                                ->distinct()
                                ->orderBy(DB::raw('RAND()'))
                                ->withCount('likes')
                                ->take(10)
                                ->leftJoin(
                                    DB::raw("( select *
                                               from user_like_mood 
                                               where user_id = '" . $userId . "' )  user_like_mood"), 'moods.id', '=', 'user_like_mood.mood_id')
                                ->get();
        $request = [
            "message" => "Moods Suggest success!",
            "moods"=> $moodsSuggest 
        ];

        return response()->json($request,200);
    }

    public function reportMood(Request $request){
        $request->validate([
            'moodId' => 'required|exists:moods,id|string',
            'report' => 'required|exists:reports,code|string',
        ]);

        $user = null;
        $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null );
        $userId = ( $user ? $user->id : "0" );

        if ( !$user )
            return  response()->json([
                'message' => "Login required!"
            ],403);

        $mood = Mood::findOrFail($request->input('moodId'));

        $reportExist = DB::table('users_report_moods')->select('user_id', 'mood_id', 'report_id')
                                                ->where('user_id',$userId)
                                                ->where('mood_id',$mood->id)
                                                ->get()->first();

        if ( $reportExist )
            return  response()->json([
                'message' => "Mood reported yet!",
                'report' => $reportExist
            ],403);
        else{

            DB::table('users_report_moods')->insert([
                'user_id' => $userId, 
                'mood_id' => $mood->id,
                'report_id' => $request->input('report'),
                'created_at' => date('Y-m-d H:m:s')
            ]);

            $mood->reports_count = $mood->reports_count + 1;
            $mood->save();
        }

        return  response()->json([
            'message' => "Report Success!",
            'user' => $user,
            'reports' => $request->input('report')
        ],200);
    }

    public function indexSkip(Request $request){
        $filterOrderValidation = ['best','asc','desc'];
        $filterUserMood = ['pref','own'];
        $reportCountLimit = 50; 

        $request->validate([ 
            'page' => 'required|numeric',
            'userMood' => Rule::in($filterUserMood),
            'userMood' => 'nullable',
            'filter.days' => 'nullable|numeric|min:0|max:365',
            'filter.order' => Rule::in($filterOrderValidation),
            'filter.order' => 'nullable',
            'filter.category' => 'exists:category,id|nullable'
        ]);

        $filtroDay = $this->getDaysFilter( $request->input('filter.days') );
        $filterOrder = $this->getOrderFilter( $request->input('filter.order') );
        $filterOrderBy = $this->getOrderByFilter( $request->input('filter.order') );
        $filterPreferedOrOwn = "1 = 1";
        $filterReportMood = $this->getReportCountFilter($reportCountLimit, $request->input('userMood') );
        $filterCategory = $this->getCategoryFilter( $request->input('filter.category') );

        $user = null;
        $userId = "0";

        $skip = $request->input('page') * 20;
        $take = 20;
        
        try{
            $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null );
            $userId = ( $user ? $user->id : "0" );
            if ( $userId && $userId != "0")
                $filterPreferedOrOwn = $this->getPreferedOrOwnFilter( $request->input('userMood'), $userId );
        }catch(TokenExpiredException $e){    }

        $moods = Mood::skip($skip)->take($take)
                        ->leftJoin(
                            DB::raw("( select *
                                       from user_like_mood 
                                       where user_id = '" . $userId . "' )  user_like_mood"), 'moods.id', '=', 'user_like_mood.mood_id')
                        ->select('user_like_mood.user_id as liked', 'moods.*')
                        ->withCount('likes')
                        ->whereRaw( $filtroDay . ' and ' . $filterPreferedOrOwn . ' and ' . $filterReportMood )
                        ->whereRaw( $filterCategory )
                        ->orderBy( $filterOrderBy, $filterOrder )
                        ->get();

        $reports = Report::all();

        $categories = Category::all();

        $moods = $moods->map(function ($item, $key) use ($reportCountLimit) {
            if ( $item->reports_count > $reportCountLimit )
                $item->reported = true;
            else   
                $item->reported = false;
            return $item;
        });

        return  response()->json([
            'moodsList' => $moods,
            'user' => $user,
            'reports' => $reports,
            'categories' => $categories
        ]);
    }

    public function getMoodUserCount(){
        $maxMoodForDay = 4;
        $userId = 0;
        try{
            $user = ( JWTAuth::getToken() && JWTAuth::parseToken()->authenticate() ? JWTAuth::parseToken()->authenticate() : null );
            $userId = ( $user ? $user->id: 0) ;

        }catch(TokenExpiredException $e){   
            return  response()->json([
                'number' => 0,
                'maxMoodToday' => $maxMoodForDay
            ]);
         }

        $users = DB::table('moods')
                    ->where('user_id',$userId)
                    ->whereRaw('DATE(created_at) = CURDATE()')
                    ->count();

        return  response()->json([
                        'number' => $users,
                        'maxMoodToday' => $maxMoodForDay
                    ]);

    }

    // Filter getAllMood
        private function getDaysFilter( $pDays ){
            $today = date("Y-m-d H:i:s") ;
            $daystosum = '30';
            $date = date("Y-m-d H:i:s", strtotime($today.' - '.$daystosum.' days'));

            $filtroDay = "moods.created_at >= '" . ( $date ) . "'";
            if ( $pDays ){
                $date = date("Y-m-d H:i:s", strtotime($today.' - '. $pDays .' days'));
                $filtroDay = "moods.created_at >= '" . ( $date ) . "'";
            }
            
            return $filtroDay;
        }

        private function getOrderFilter( $pOrder ){
            $order = "";
            if ( $pOrder && $pOrder <> 'pref' )
                $order = $pOrder;
            
            return $order;
        }

        private function getPreferedOrOwnFilter( $pPrefered, $pUserId ){
            $filter = "1 = 1";
            if ( $pPrefered == "pref" )
                $filter = "user_like_mood.user_id IS NOT NULL";
            if ( $pPrefered == "own" )
                $filter = "moods.user_id = " . $pUserId;

            return $filter;
        }

        private function getOrderByFilter ( $pOrder ){
            $filter = "created_at";
            if ( $pOrder == "best" )
                $filter = "likes_count";
            return $filter;
        }

        private function getReportCountFilter($limit, $ownMood){
            $result = "moods.reports_count <= " . $limit;

            if ( $ownMood == "own")
                return "1 = 1";

            return $result;
        }

        private function getCategoryFilter($categoryId){
            $result = "1 = 1";

            if ( $categoryId ){
                if ( $categoryId == 1){
                    return $result;
                }else{
                    $result = "category_id = " . $categoryId;
                }
            }

            return $result;
        }
    // End - Filter getAllMood
}
