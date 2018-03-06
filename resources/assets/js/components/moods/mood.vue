<template src="../../../../views/components/moods/mood.html"></template>
<script>
    import {HttpMoonre} from './../../common/http';

    export default {
        props: ['pMood', 'pReportList', 'pCategories' ],

        data(){
            return{
                liked: this.pMood.liked,
                likeNumber: this.pMood.likes_count || 0,

                userId: -1, 

                deleteModal: false,
                reportModal: false,

                editMoodModalActive: false,

                httpMoonre: new HttpMoonre(),

                toast: false,
                toastMessage: '',

                reportMood: '',
                reportsList: this.pReportList,
                
                category: null,

                mood_message: this.pMood.message,

                commentIdentifier: 'http://moonre.it/mood/' + this.pMood.id,
            }
        },

        watch: {
            pMood(){
                this.liked = this.pMood.liked;
                this.likeNumber = this.pMood.likes_count;
                this.commentIdentifier = 'http://moonre.it/mood/' + this.pMood.id;
                this.updateCategory();
            },
            pReportList(){
                this.reportsList = this.pReportList;
            },
            pCategories(){
                this.updateCategory();
            }
        },

        created(){

            if ( this.pCategories )
                this.pCategories.map( x => {
                    if ( x.id == this.pMood.category_id )
                        this.category = x;
                });
            
            var user = JSON.parse(localStorage.getItem("user"));

            if ( user )
                this.userId = user.id;
        },

        methods:{
            likeClick(  ){
                if ( !this.liked )
                    this.addLike()
                else
                    this.removeLike()            
            },
            addLike(){
                let data = {'idMood': this.pMood.id };
                this.httpMoonre.post( this.httpMoonre.getLink( this.httpMoonre.like ), data )
                               .then( response => {
                                    this.liked = true;
                                    this.likeNumber = this.likeNumber + 1;         
                               })
                               .catch( response => {
                                    if ( response.data.error == "token_not_provided" || response.data.error == "token_expired" ){
                                        this.toast= true;
                                        this.toastMessage= 'Login required';
                                    }

                               });

            },

            removeLike(){
                this.httpMoonre.delete( this.httpMoonre.getLink( this.httpMoonre.like ), this.pMood.id )
                               .then( response => {
                                        this.liked = false;
                                        this.likeNumber = this.likeNumber - 1;
                               })
                               .catch( response => {
                                    this.toastMessage = response.data.message;
                                    this.toast = true;
                               });
            },

            deleteMood(){
                this.httpMoonre.delete( this.httpMoonre.getLink( this.httpMoonre.mood ), this.pMood.id )
                               .then( response => {
                                    this.toastMessage = response.data.message;
                                    this.toast = true;
                                    AppEvent.$emit('deleteMood', this.pMood.id);
                               })
                               .catch( response => {
                                    this.toastMessage = response.data.message;
                                    this.toast = true;
                               });
            },

            openMoodDetail(){
                this.$router.push({ path: '/mood/' + this.pMood.id });
                window.scrollTo(0,0);
            },

            deactiveEditMoodModal(response){
                this.editMoodModalActive = false;
                if ( response && response.message ){
                    this.toastMessage = response.message;
                    this.toast = true;
                    AppEvent.$emit('updateMasonry');
                }
                if (!response){
                    this.pMood.message = this.mood_message;
                    AppEvent.$emit('updateMasonry');
                }
            },

            activeEditMoodModal(){
                this.editMoodModalActive = true;
            },

            sendReportMood(){
                let data = {
                        'moodId': this.pMood.id ,
                        'report': this.reportMood
                    };
                this.httpMoonre.post( this.httpMoonre.getLink( this.httpMoonre.moodReport ), data )
                               .then( response => {
                                    this.toast= true;
                                    this.toastMessage= response.data.message;       
                               })
                               .catch( response => {
                                    this.toast= true;
                                    this.toastMessage= response.data.message;  
                               });
            },

            updateCategory(){
                this.category = null;
                if ( this.pCategories )
                    this.pCategories.map( x => {
                        if ( x.id == this.pMood.category_id )
                            this.category = x;
                    });
            }
        }
    }
</script>