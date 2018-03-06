
<template src="../../../views/layout/header.html"></template>

<script>
    export default {
        data(){
            return{
                logged: false,
                userName: "un"
            }
        },
        methods:{
            logout(){
                localStorage.removeItem('token');
                location.reload();
            },
            menuShow(){
                AppEvent.$emit('navigationLeftShow');
                AppEvent.$emit('updateMasonry');       
            }
        },
        created(){
            let token = localStorage.getItem("token");
            if ( token )
                this.logged = true;

            let user = JSON.parse(localStorage.getItem("user"));
            if ( user && user.email )
                this.userName = user.email.substring(0,2).toUpperCase();

        }

    }
</script>