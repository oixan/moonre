<template src="../../../../views/components/moods/addMood-modal.html"></template>
<script>
    import {HttpMoonre} from './../../common/http';

    export default {
        props: ['visible', 'pMode', 'pMood', 'pCategory'],
        data(){
            return{
                mood: null,
                title: '',
                enableVisible: this.visible,
                valueReturn: null, 
                moodsUserToday: 0,
                maxMoodToday: 0,

                httpMoonre: new HttpMoonre(),

                category: '',
                categories: this.pCategory
            }
        },

        watch: {
            pCategory(){
                this.categories = this.pCategories;
            }
        },

        created(){
            this.getMoodsUserToday();

            if ( this.pMode == 1 )
                this.categories.map( x => {
                            if ( x.id == this.pMood.category_id )
                                this.category = x.description;
                        });

            if ( this.pMood ) 
                this.mood = this.pMood;
            else
                this.mood = {
                    'message': ''   
                }

            if ( this.pMode == 0 )
                this.title = "Insert a MOOD to share.";
            else
                if ( this.pMode == 1)
                    this.title = "Edit the MOOD.";
        },

        methods:{
            closeModal(){
                this.$emit('closeModal', this.valueReturn );
            },


            dispatchSave(){
                if ( this.pMode == 0 )
                    this.saveMood();
                else    
                    if ( this.pMode == 1  )
                        this.updateMood();
            },

            saveMood(){
                let category_id = '';
                if (  this.categories )
                    this.categories.map( x => {
                        if ( x.description == this.category )
                            category_id = x.id;
                    });

                let data = { 
                    'message': this.mood.message,
                    'message2': this.mood.message2,
                    'category_id': category_id
                };
                this.httpMoonre.post( this.httpMoonre.getLink(this.httpMoonre.mood),  data )
                                .then( response => {
                                    if ( response.data.message ){
                                        this.valueReturn = { 'status': response.data.status, 'message': response.data.message, 'mood': response.data.mood };
                                        this.closeModal();
                                    }
                                    console.log(response);
                                })
                                .catch( response => {
                                    if( response.data.message ){
                                        this.valueReturn = { 'status': 403, 'message': response.data.message };
                                        this.closeModal();
                                    }

                                    console.log(response);
                                });
            },

            updateMood(){
                let category_id = '';
                if (  this.categories )
                    this.categories.map( x => {
                        if ( x.description == this.category )
                            category_id = x.id;
                    });

                let data = { 
                    'message': this.mood.message,
                    'message2': this.mood.message2,
                    'category_id': category_id
                };

                this.httpMoonre.put( this.httpMoonre.getLink(this.httpMoonre.mood), this.pMood.id , data )
                                .then( response => {
                                    if ( response.data.message ){
                                        this.valueReturn = { 'status': response.data.status, 'message': response.data.message, 'mood': response.data.mood };
                                        this.closeModal();
                                    }
                                    console.log(response);
                                })
                                .catch( response => {
                                    if( response.data.message ){
                                        this.valueReturn = { 'status': response.data.status, 'message': response.data.message };
                                        this.closeModal();
                                    }

                                    console.log(response);
                                });
            },

            getMoodsUserToday(){
                this.httpMoonre.get( this.httpMoonre.getLink(this.httpMoonre.moodUserCount) )
                                .then( response => {
                                    if ( response.data.number || response.data.number == 0 ){
                                        this.moodsUserToday = response.data.number;
                                        this.maxMoodToday = response.data.maxMoodToday;
                                    }
                                })
                                .catch( response => {                    
                                    console.log(response);
                                });
            }
        },
        
        computed:{
            maxMoodsReaced(){
                return this.moodsUserToday >= this.maxMoodToday ;
            }
        }
    }
</script>