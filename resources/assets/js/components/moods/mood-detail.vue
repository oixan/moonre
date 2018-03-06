<template src="../../../../views/components/moods/mood-detail.html"></template>
<script>
    import {HttpMoonre} from './../../common/http';
    import VueDisqus from 'vue-disqus/VueDisqus.vue'

    export default {
        data(){
            return{
                displayCommment: false,
                commentIdentifier: '',

                mood: {
                    message: "",
                },
                moodsSuggest: null,

                reportList: [],
                categories: [],

                httpMoonre: new HttpMoonre()
            }
        },

        mounted(){
            this.getMood();
            this.getMoodsSuggestion();
        },

        methods:{
            getMood(){
                this.httpMoonre.get( this.httpMoonre.getLink( this.httpMoonre.mood ) + "/" + this.$route.params.id )
                               .then( response => {
                                        this.mood = response.data.mood;
                                        this.reportList = [];
                                        this.categories = [];
                                        
                                        this.categories = response.data.categories;

                                        for ( let entry of response.data.reports ) {
                                            this.reportList.push(entry);
                                        }
                                        
                                        this.resetComment();
                               })
                               .catch( response => {
                               });
            },

            getMoodsSuggestion(){
                this.httpMoonre.get( this.httpMoonre.getLink( this.httpMoonre.moodSuggest ) )
                               .then( response => {
                                   this.moodsSuggest = response.data.moods;
                               })
                               .catch( response => {
                               });
            },

            resetComment(){
                this.displayCommment = false;
                this.commentIdentifier = 'http://moonre.it/mood/' + this.mood.id;
                setTimeout( () => {
                    this.displayCommment = true;
                    setTimeout( () => { window.DISQUSWIDGETS.getCount({reset: true}) }, 300 )   
                },500)
            }
        },

        components: {
            VueDisqus
        },

        watch: {
            '$route' (to, from) {
                this.getMood();
                this.getMoodsSuggestion();    
                this.resetComment();
            }
        }
    }
</script>