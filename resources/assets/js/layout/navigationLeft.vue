
<template src="../../../views/layout/navigationLeft.html"></template>

<script>
    export default {
        data(){
            return{
                showNavigation: false,
            }
        },
        
        created(){

            AppEvent.$on('navigationLeftShow', () => {
                this.showNavigation = !this.showNavigation;
            })
        },

        methods:{

        },

        watch: {
            '$route' (to, from) {
                // this.showNavigation = false;
            }
        }

    }
</script>