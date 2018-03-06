
<template src="../../../views/login/login.html"></template>

<script>
    import {HttpMoonre} from './../common/http';

    export default {
        data(){
            return{
                email: '',
                password: '',
                httpMoonre: new HttpMoonre(),

                toast: false,
                toastMessage: '',
            }
        },
        methods:{
            login(){
                var data = {
                    'email': this.email,
                    'password': this.password
                };
                this.httpMoonre.post( this.httpMoonre.getLink(this.httpMoonre.login) , data )
                                .then( response => {
                                    if ( response.data.token ){
                                        localStorage.setItem('token', response.data.token);
                                        localStorage.setItem('user', JSON.stringify(response.data.user) );
                                        this.toast = true;
                                        this.toastMessage = response.data.message;                                      
                                        setTimeout( x=> {
                                            this.$router.push({ path: "/" })
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