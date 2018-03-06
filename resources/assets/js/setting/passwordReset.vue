
<template src="../../../views/setting/passwordReset.html"></template>

<script>
    import {HttpMoonre} from './../common/http';


    export default {
        data(){
            return{
                oldPassword: '',
                newPassword: '',
                confirmNewPassword: '',

                httpMoonre: new HttpMoonre(),

                toast: '',
                toastMessage: '',
            }
        },
        methods:{
            resetPassword(){
                var data = {
                    'oldPassword': this.oldPassword,
                    'newPassword': this.newPassword,
                    'confirmNewPassword': this.confirmNewPassword,
                };

                this.httpMoonre.post( this.httpMoonre.getLink(this.httpMoonre.userPasswordReset), data )
                            .then( response => {
                                if ( response.data.message ){
                                    this.toastMessage = response.data.message;
                                    this.toast = true;

                                    this.oldPassword = this.newPassword = this.confirmNewPassword = '';
                                }
                                console.log(response);
                            })
                            .catch( erros => {
                                if ( erros.data.errors ){
                                    let message = '';
                                    Object.keys(erros.data.errors).forEach(key => {
                                                                   message = erros.data.errors[key][0]; 
                                                            });
                                    if ( message ){
                                            this.toastMessage = message;
                                            this.toast = true;
                                    }
 
                                }else{
                                    if ( erros.data.message ){
                                        this.toastMessage = erros.data.message;
                                        this.toast = true;                             
                                    }
                                }

                                this.oldPassword = this.newPassword = this.confirmNewPassword = '';
                                console.log(erros);
                            });
            }
        }
    }
</script>