
<template src="../../../../../views/components/navigations/left/home.html"></template>

<script>
    export default {
        data(){
            return{
                userFilter: null,
                logged: false
            }
        },
        created(){
            let token = localStorage.getItem("token");
            if ( token )
                this.logged = true;
        },
        methods:{
            setFilter( pFilter ){
           
                let filter = {
                    'user': this.userFilter
                }
                if ( pFilter)
                    filter.reset = pFilter.reset;

                this.$router.push({ path: '/' });
                setTimeout( () => {
                    AppEvent.$emit("listMoodFilter", filter);
                },500)
                
            },

            initFilter(){
                this.userFilter = null;
            },

            initSetFilter(){
                let filter = {
                    'reset': {
                        timeFilter: null,
                        orderFilter: null,
                        categoryFilter: null
                    }
                }

                this.initFilter();
                this.setFilter(filter);
            }
        },

        watch:{
            '$route' (to, from) {

                if ( to.path != "/" )
                    this.initFilter();
            }
        }

    }
</script>