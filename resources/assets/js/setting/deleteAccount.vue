
<template src="../../../views/setting/deleteAccount.html"></template>

<script>
    import {HttpMoonre} from './../common/http';

    export default {
        data(){
            return{
                password: '',
                httpMoonre: new HttpMoonre(),

                toastMessage: '',
                toast: '',
            }
        },
        methods:{
            deleteUser(){

                var data = {
                    'password': this.password
                }

                var idUser = JSON.parse(localStorage.getItem('user')).id;

                this.httpMoonre.post( this.httpMoonre.getLink(this.httpMoonre.userDelete) + '/' + idUser  , data )
                               .then( response => {
                                    localStorage.removeItem("token");
                                    localStorage.removeItem("user");
                                   
                                    this.toastMessage = response.data.message;
                                    this.toast = true; 

                                    setTimeout( ()=> { location.reload(); }, 2000 );
                               })
                               .catch( errors => {
                                    if ( errors.data ){
                                        this.toastMessage = errors.data.message;
                                        this.toast = true; 
                                    }
                               })
            }
        }
    }
</script>