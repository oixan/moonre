
<template src="../../../views/logon/logon.html"></template>

<script>
    import {HttpMoonre} from './../common/http';

    export default {
        data(){
            return{
                email: '',
                password: '',
                password_confirm: '',
                term: null,

                httpMoonre: new HttpMoonre(),

                toastMessage: '',
                toast: false,
            }
        },
        methods:{
            logon(){
                var data = {
                    'email': this.email,
                    'password': this.password,
                    'password_confirm': this.password_confirm,
                    'term': this.term
                };
                this.httpMoonre.post( this.httpMoonre.getLink(this.httpMoonre.logon) , data )
                                .then( response => {
                                    if ( response.data.message ){
                                        this.toast = true;
                                        this.toastMessage = response.data.message;                                      
                                        setTimeout( x=> {
                                            this.$router.push({ path: "/login" })
                                        },2000);                                    
                                    }
                                    console.log(response);
                                })
                                .catch( response => {
                                    this.toast = true;
                                    this.toastMessage = response.data.message;
                                    console.log(response);
                                });
            }
        }
    }
</script>