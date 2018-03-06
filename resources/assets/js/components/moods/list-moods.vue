<template src="../../../../views/components/moods/list-moods.html"></template>

<script>
    import {HttpMoonre} from './../../common/http';
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        data(){
            return{
                addMoodModalActive: false,
                moods: [],
                masonry: null,
                httpMoonre: new HttpMoonre(),

                orderFilter: null,
                timeFilter: null,
                userFilter: null,
                categoryFilter: null,

                reportsList: [],

                infiniteScrollPage: 0,

                filter:{ 'page': 0 },

                swiperOption: {
                    slidesPerView: 7,
                    spaceBetween: 15,

                    breakpoints: {
                        600: {
                            slidesPerView: 2,
                            spaceBetween: 10
                        },
                        960: {
                            slidesPerView: 4,
                            spaceBetween: 15
                        },
                        1264: {
                            slidesPerView: 5,
                            spaceBetween: 15
                        },
                        1904: {
                            slidesPerView: 7,
                            spaceBetween: 15
                        }
                    },

                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },

                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    }
                },

                categories:[],

                toast: false,
                toastMessage: ""
            }
        },

        mounted(){
            //get Moods list
            //this.getMoodsList();

            AppEvent.$on("listMoodFilter", (filter) => { 
                this.moods = [];
                this.infiniteScrollPage = 0;

                this.filter = {
                    'page': this.infiniteScrollPage,
                    'filter': {}
                };

                if ( filter && filter.reset && filter.reset.hasOwnProperty('orderFilter') )
                    this.orderFilter = filter.reset.orderFilter;

                if ( filter && filter.reset && filter.reset.hasOwnProperty('timeFilter') )
                    this.timeFilter = filter.reset.timeFilter;

                if ( filter && filter.reset && filter.reset.hasOwnProperty('categoryFilter') )
                    this.categoryFilter = filter.reset.categoryFilter;

                this.filter['filter']['order'] = this.orderFilter;

                this.filter['filter']['days'] = this.timeFilter;

                this.filter['filter']['category'] = this.categoryFilter;

                if ( filter )
                    this.userFilter = filter.user;
                this.filter['userMood'] = this.userFilter;
                

                this.updateMasonry();
                setTimeout( x => {
                            this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset'); // emit the event manually
                    },500)
            });

            AppEvent.$on('updateMasonry',  () => { this.updateMasonry() } );

            AppEvent.$on('deleteMood',  (idMood) => { this.deleteMood(idMood) } );
        },

        methods:{
            activeAddMood(){
                this.addMoodModalActive = true;
            },

            deactiveAddMood( response ){
                if ( response && response.message ){
                    if ( response.mood ){
                        this.moods.push( response.mood );
                        setTimeout( x => {
                            this.masonry.reloadItems();
                            this.masonry.layout();
                        },100)
                    }
                    this.toastMessage = response.message;
                    this.toast = true;
                }
                this.addMoodModalActive = false;
            },

            getMoodsList( $state ){

                this.filter['page'] = this.infiniteScrollPage;
                this.infiniteScrollPage++;

                this.httpMoonre.post( this.httpMoonre.getLink( this.httpMoonre.moodSkip ),  this.filter)
                    .then( response => {
                        // this.moods = [];
                        this.reportsList =  response.data.reports;
                        this.categories = response.data.categories;

                        response.data.moodsList.map( x => {
                            if ( response.data.user && x.liked == response.data.user.id ) 
                                x['liked'] = true;
                            this.moods.push(x);   
                        });
                        
                        setTimeout( x => {
                                this.initMasonry();         

                                setTimeout( () => {
                                    if ( $state && response.data.moodsList.length != 0)     
                                        $state.loaded();   
                                    else
                                        if ( $state && response.data.moodsList.length == 0)
                                               $state.complete();  

                                },200); 

                                setTimeout( () => { 
                                    if ( window.DISQUSWIDGETS )
                                        window.DISQUSWIDGETS.getCount({reset: true}) 
                                }, 300 )     
                            });
                        console.log(response);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            initMasonry(){
                // masonry init
                    // this.masonry.reloadItems();
                    //  this.masonry.layout();
                this.masonry = new Masonry( '.masonry-grid', {
                    // options
                    itemSelector: '.masonry-grid-item',
                    columnWidth: '.masonry-grid-sizer',
                    gutter: 30,
                });
            },

            updateMasonry(){
                setTimeout( () => {
                    this.masonry.reloadItems();
                    this.masonry.layout();
                },300);
            },

            infiniteHandler($state){
                this.getMoodsList( $state );
                // $state.loaded();
            },

            deleteMood(idMood){
                var index = 0;
                this.moods.map( x => {
                    if ( x.id == idMood ){
                        this.moods.splice(index, 1);
                        this.updateMasonry();
                    }
                    index++;
                })
                setTimeout(function () {
                        if (window.DISQUSWIDGETS) window.DISQUSWIDGETS.getCount({ reset: true });
                    }, 300);
            },

            setFilter( ){
                let filter = {
                    'order': this.orderFilter,
                    'time': this.timeFilter,
                    'user': this.userFilter,
                    'category': this.categoryFilter,
                }

                AppEvent.$emit("listMoodFilter", filter);
            },

        },

        components: {
            InfiniteLoading
        },
    }
</script>