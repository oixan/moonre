
<template src="../../../views/setting/user.html"></template>

<script>
    import {HttpMoonre} from './../common/http';

    export default {
        data(){
            return{
                id: 1,
                name: null,
                surname: null,
                sex: null,
                city: null,
                country: null,
                address: null,

                cityList: [],
                countryList: [],
                cityListOrigin: [],
                countryListOrigin: [],

                httpMoonre: new HttpMoonre(),

                toast: '',
                toastMessage: '',
            }
        },

        created(){
            var user = JSON.parse(localStorage.getItem('user'));
            this.id = user.id;

            if (  this.id )
                this.httpMoonre.get( this.httpMoonre.getLink(this.httpMoonre.user + '/' + this.id) )
                                    .then( response => {
                                        this.name = response.data.user.name;
                                        this.surname = response.data.user.surname;
                                        this.sex = response.data.user.sex;

                                        let cityString = ( response.data.user.city? response.data.user.city.city : "" );
                                        setTimeout( x => {
                                            this.city = { 'id': response.data.user.city_id,  'city': cityString }; 
                                        }, 500)
                                        
                                        let countryString = ( response.data.user.country_extended? response.data.user.country_extended[0] : "" );
                                        this.country = { 'code': response.data.user.country,  'value': countryString };
                                        this.address = response.data.user.address;
                                        console.log(response);
                                    })
                                    .catch( errors => {
                                        console.log(erros);
                                    })

                this.httpMoonre.get( this.httpMoonre.getLink(this.httpMoonre.country) )
                                    .then( response => {
                                        this.countryList = response.data.country;
                                        this.countryListOrigin = response.data.country;
                                        
                                        console.log(response);
                                    })
                                    .catch( errors => {
                                        console.log(erros);
                                    })
        },

        methods:{
            updateUser(){
                var data={ };

                data.name = null;
                if ( this.name )
                    data.name = this.name;

                data.surname = null;
                if ( this.surname )
                    data.surname = this.surname;
          
                if ( this.sex )
                    data.sex = this.sex;

                data.country = null;
                if ( this.country && this.country.code )
                    data.country = this.country.code;
                else if ( this.country  && !( this.country instanceof Object ) )
                        data.country = this.country;
                    else   
                        data.county = null;
                        
                data.city_id = null;
                if ( this.city && this.city.id )
                    data.city_id = this.city.id;
                else if ( this.city && !( this.city instanceof Object ) )
                        data.city_id = this.city;
                    else    
                        data.city_id = null;              

                data.address = null;
                if ( this.address )
                    data.address = this.address;

                this.httpMoonre.put( this.httpMoonre.getLink(this.httpMoonre.user), this.id, data )
                                .then( response => {
                                    if ( response.data.message ){
                                        this.toastMessage = response.data.message;
                                        this.toast = true;
                                    }
                                    console.log(response);
                                })
                                .catch( errors => {
                                    console.log(erros);
                                })
            }
        },

        watch:{
            country: _.debounce( function() {
                    this.city = null;
                    this.cityList = [];
                    this.cityListOrigin = [];
                    if ( this.country ) {
                        if (  this.country.code )
                            this.country = this.country.code;

                        this.httpMoonre.get( this.httpMoonre.getLink(this.httpMoonre.city + "/" + this.country ) )
                                            .then( response => {                                
                                                this.cityList = response.data.cities;
                                                this.cityListOrigin = response.data.cities;
                                    
                                                console.log(response);
                                            })
                                            .catch( errors => {
                                                console.log(errors);
                                            })
                    }
                }, 200)
            
        }
    }
</script>