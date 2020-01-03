<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <table class="table" v-if="cryptoCurrencies">
                    <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Market Cap</th>
                        <th>Volume(24h)</th>
                        <th>Change(24h)</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="cryptoCurrency in cryptoCurrencies">
                        <td>{{cryptoCurrency.cmc_rank}}</td>
                        <td >{{cryptoCurrency.name}}</td>
                        <td>$ {{cryptoCurrency.quote.USD.price}}</td>
                        <td>$ {{cryptoCurrency.quote.USD.market_cap}}</td>
                        <td>$ {{cryptoCurrency.quote.USD.volume_24h}}</td>
                        <td v-if="cryptoCurrency.quote.USD.percent_change_24h < 0" style="color: red">{{cryptoCurrency.quote.USD.percent_change_24h}} %</td>
                        <td v-if="cryptoCurrency.quote.USD.percent_change_24h > 0" style="color: green">{{cryptoCurrency.quote.USD.percent_change_24h}} %</td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div v-if="!cryptoCurrencies">
                something wrong
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "Currency",
        data: function () {
            return {
                cryptoCurrencies: [],
            }
        },
        created: function () {
            this.getCrypto();
        },
        methods: {
            //get crypto currency (bitcoin)
            getCrypto(){
                const vm = this;
                const id = 5;
                axios.get('/currency')
                    .then(function (response) {
                        vm.cryptoCurrencies = response.data.data;
                    })
                    .catch(function (error) {
                         // console.log(error);
                    })

            },
        },
    }
</script>
