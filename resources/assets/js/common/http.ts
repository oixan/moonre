import  axios  from "axios";

export class HttpMoonre{

    linkDev = "localhost:8000";
    linkProd = "www.moonre.it";

    public linkWeb = this.linkProd;
    public linkApi = "/" + "api/v1/";

    public login = "signin";
    public logon = "signup";

    public mood = "mood";
    public moodSkip = "moodSkip";
    public moodUserCount = "moodUserCount";
    public moodSuggest = "moodSuggest";
    public moodReport = "moodReport";

    public like = "like";

    public user = "user";
    public userPasswordReset = this.user + "/resetPassword";
    public userDelete = this.user + "/destroyWithPassword";

    public country = "country";
    public city = "city";

    constructor (){

    }

    public getLink( path: string = ""){   
        return this.linkApi + path;
    }

    public get(path:string){
        this.aggiornaHeader();
        return new Promise( (resolve, reject) =>{
                axios.get(path)
                    .then( response => {
                        resolve(response);
                    })
                    .catch( (error) => {
                        this.checkErrors(error);
                        reject(error.response) ;
                    });
        } );
    }

    public post(path:string, data){
        this.aggiornaHeader();
        return new Promise( (resolve, reject) => {
                axios.post(path, data)
                    .then( response => {
                        resolve(response);
                    })
                    .catch( (error) => {
                        this.checkErrors(error);
                        reject(error.response) ;
                    });
        } );
    }

    public put(path:string, id, data){
        this.aggiornaHeader();
        return new Promise( (resolve, reject) => {
                axios.put(path + '/' + id, data)
                    .then( response => {
                        resolve(response);
                    })
                    .catch( (error) => {
                        this.checkErrors(error);
                        reject(error.response) ;
                    });
        } );
    }

    public delete(path:string, id){
        this.aggiornaHeader();
        return new Promise( (resolve, reject) =>{
                axios.delete( path + "/" + id )
                    .then( response => {
                        resolve(response);
                    })
                    .catch( (error) => {
                        this.checkErrors(error);
                        reject(error.response) ;
                    });
        } );
    }

    public aggiornaHeader(){
        window['axios'].defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window['axios'].defaults.headers.common['Accept'] = 'application/json';
        window['axios'].defaults.headers.common['Content-Type'] = 'application/json';
        window['axios'].defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');
    }

    private checkErrors(error){
        
        /* if ( ( (error.response.data.error == "token_expired" && localStorage.getItem("token")) || error.response.data.error == "token_not_provided" ) && 
             ( localStorage.getItem("token") || localStorage.getItem("user") ) 
           ) */
        
        
        if(  error.response.data.error == "token_expired" || 
             error.response.data.error == "token_not_provided" || 
             error.response.data[0] == "token_expired" )
            { 
                if ( !localStorage.getItem("token") && !localStorage.getItem("user") ){
                    error.response.message = "Login Required"
                    error.response.data.message = "Login Required"
                    return ;
                }

                localStorage.removeItem("user");    
                localStorage.removeItem("token");

                setTimeout( () => { 
                    window.location.replace(this.linkWeb);
                    location.reload();
                }, 2000);
            }
    }

}