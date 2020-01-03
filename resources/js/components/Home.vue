<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-6 offset-3" style="margin: auto; background: white; padding: 20px; box-shadow: 10px 10px 5px #888a85">
                    <div class="panel-heading">
                        <h4>Linkedin & Coin Market API App</h4>
                        <p style="font-style: italic">Sign with Linkedin to view crypto money list</p>
                    </div>
                    <hr>
                    <div class="panel-body">
                        <button  class="btn btn-primary btn-block" v-on:click="login()">Sign In with LinkedIn</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
            }
        },
        created: function () {
            this.getUrl();
        },
        methods: {
            login(){
                window.location.href = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=86u55eqvp2d1lh&redirect_uri=http://127.0.0.1:8000&state=fooobar&scope=r_liteprofile%20r_emailaddress%20w_member_social"
            },
            getUrl(){
                if(window.location.search){
                    axios.get('/login')
                        .then(function (response) {
                            window.location.href = 'http://127.0.0.1:8000/#/cryptoCurrency'

                        })
                        .catch(function (error) {
                            // console.log(error);
                        })
                }
            }
        },
    }
</script>
<style>
    body, html {
        height: 100%;
    }
</style>
