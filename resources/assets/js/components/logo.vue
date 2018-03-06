
<template src="../../../views/components/logo.html"></template>

<script>
    export default {
        props: { 
            'forceLg': Boolean,
            'forceSm': Boolean,
            'pWidth': Number,
        },

        data(){
            return{
                width: 'initial',
                widthMini: 'initial',

                forceLargeLogo: false,
                forceSmallLogo: false,
            }
        },
        methods:{
            loadHomepage(){
                location.reload();
            }
        },
        created(){
            if ( this.forceLg )
                this.forceLargeLogo = true;
            
            if ( this.forceSm )
                this.forceSmallLogo = true;

            if ( this.pWidth ){
                this.width = this.pWidth + 'px';
                this.widthMini = (this.pWidth/6 + 5) + 'px' ;
            }
        }
    }
</script>