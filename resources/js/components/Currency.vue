<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-2"></div>
            <div v-if="crypto" class="col-sm-3">
                {{crypto.name}}
            </div>
            <div class="col-sm-3" v-if="crypto.quote">
                $ {{crypto.quote.USD.price}}
            </div>
            <div v-if="!crypto">
                something wrong
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-3">
                <b-form-select
                    v-model="selected"
                    :options="options"
                    value-field="item"
                    text-field="name"
                    disabled-field="notEnabled"
                ></b-form-select>
            </div>
            <div class="col-sm-3" v-if="selected === 'USD'">
                <div>
                    <b-form-input name="USD" type="number" v-model="usd" placeholder="25-10000 USD"></b-form-input>
                </div>
            </div>
            <div class="col-sm-3" v-if="selected === 'Coin'">
                <div>
                    <b-form-input name="Coin" type="number" v-model="amount" placeholder="Coin"></b-form-input>
                </div>
            </div>
            <div class="col-sm-3">
                <div>
                    <b-button lg="4" class="pb-md-1" variant="primary" v-if="selected === 'USD' && !priceConversion.quote" v-on:click="getPriceUSD(usd)">Buy</b-button>
                </div>
                <div>
                    <b-button lg="4" class="pb-md-1" variant="primary" v-if="selected === 'Coin' && !priceConversion.quote" v-on:click="getPriceConversion(amount)">Buy</b-button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-7 margin-top-10" v-if="!creditCardFormStatus">
                <b-alert
                    :show="dismissCountDown"
                    dismissible
                    variant="warning"
                    @dismissed="dismissCountDown=0"
                    @dismiss-count-down="countDownChanged">
                    <p>min {{minUSD}}, max {{maxUSD}} USD</p>
                    <p>min {{minCoin}}, max {{maxCoin}} Coin</p>
                    <b-progress
                        variant="warning"
                        :max="dismissSecs"
                        :value="dismissCountDown"
                        height="4px"
                    ></b-progress>
                </b-alert>
            </div>
        </div>
        <div class="row" v-if="priceConversion.quote">
            <div class="col-sm-2"></div>
            <div class="col-sm-6 margin-top-10">
                <div class="card">
                    You will pay $ {{priceConversion.quote.USD.price}} for {{priceConversion.amount}} Bitcoin
                </div>
            </div>
            <div class="col-sm-3">
                <div>
                    <b-button lg="4" class="pb-md-1" variant="warning" v-if="priceConversion.quote" v-on:click="cancelBuy()">Cancel</b-button>
                </div>
            </div>
        </div>
        <div class="row" v-if="creditCardFormStatus">
            <div class="creditCardForm">
                <div class="heading">
                    <h1>Confirm Purchase</h1>
                </div>
                <div class="payment">
                    <form>
                        <div class="form-group owner">
                            <label for="owner">Owner</label>
                            <input type="text" class="form-control" id="owner" placeholder="Jhon Doe">
                        </div>
                        <div class="form-group CVV">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" placeholder="123">
                        </div>
                        <div class="form-group" id="card-number-field">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" class="form-control" id="cardNumber" placeholder="4242 **** **** ****">
                        </div>
                        <div class="form-group" id="expiration-date">
                            <label>Expiration Date</label>
                            <select>
                                <option value="01">January</option>
                                <option value="02">February </option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <select>
                                <option value="16"> 2016</option>
                                <option value="17"> 2017</option>
                                <option value="18"> 2018</option>
                                <option value="19"> 2019</option>
                                <option value="20"> 2020</option>
                                <option value="21"> 2021</option>
                            </select>
                        </div>
                        <div class="form-group" id="credit_cards">
                            <img src="../assets/images/visa.jpg" id="visa" alt="Visa">
                            <img src="../assets/images/mastercard.jpg" id="mastercard" alt="Master">
                            <img src="../assets/images/amex.jpg" id="amex" alt="Amex">
                        </div>
                        <div class="form-group" id="pay-now">
                            <button class="btn btn-default" id="confirm-purchase" v-on:click="checkout()">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "Currency",
        data: function () {
            return {
                crypto: [],
                amount: '',
                priceConversion: '',
                usd: '',
                selected: 'USD',
                dismissSecs: 10,
                dismissCountDown: 0,
                minCoin: '',
                maxCoin: '',
                minUSD: 25,
                maxUSD: 10000,
                creditCardFormStatus: false,
                options: [
                    {name: 'USD' },
                    {name: 'Coin' }
                ]
            }
        },
        created: function () {
            this.getCrypto();
        },
        methods: {
            //get crypto currency (bitcoin)
            getCrypto(){
                const vm = this;

                axios.get('/currency')
                    .then(function (response) {
                        vm.crypto = response.data.data[1];
                        vm.minCoin = vm.minUSD / vm.crypto.quote.USD.price;
                        vm.maxCoin = vm.maxUSD / vm.crypto.quote.USD.price;
                    })
                    .catch(function (error) {
                         // console.log(error);
                    })

            },
            //price conversion BTC to USD with amount
            getPriceConversion(amount){
                const vm = this;

                if(amount < vm.minCoin || amount > vm.maxCoin ){
                    this.showAlert();
                }else {
                    axios.get('/priceConversion', {
                        params: {
                            amount: amount
                        }
                    })
                        .then(function (response) {
                            vm.priceConversion = response.data.data;
                            vm.creditCardFormStatus = true;
                        })
                        .catch(function (error) {
                            // console.log(error);
                        })
                }

            },
            //get bitcoin amount from price
            getPriceUSD(usd){
                const vm = this;
                if(usd < vm.minUSD || usd > vm.maxUSD){
                    this.showAlert();
                }else {
                    this.getPriceConversion(usd / vm.crypto.quote.USD.price);
                }
            },
            cancelBuy(){
                this.priceConversion = '';
                this.creditCardFormStatus = false;
                this.dismissCountDown = 0;
                this.amount = '';
                this.usd = ''
            },
            checkout(){
                this.$router.push('success')
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown
            },
            showAlert() {
                this.creditCardFormStatus = false;
                this.dismissCountDown = this.dismissSecs
            }
        },
    }
</script>
<style>
    .margin-top-10 {
        margin-top: 10px;
    }
    .creditCardForm {
        max-width: 700px;
        background-color: #fff;
        margin: 10px auto;
        overflow: hidden;
        padding: 25px;
        color: #4c4e56;
    }
    .creditCardForm label {
        width: 100%;
        margin-bottom: 10px;
    }
    .creditCardForm .heading h1 {
        text-align: center;
        font-family: 'Open Sans', sans-serif;
        color: #4c4e56;
    }
    .creditCardForm .payment {
        float: left;
        font-size: 18px;
        padding: 10px 25px;
        margin-top: 20px;
        position: relative;
    }
    .creditCardForm .payment .form-group {
        float: left;
        margin-bottom: 15px;
    }
    .creditCardForm .payment .form-control {
        line-height: 40px;
        height: auto;
        padding: 0 16px;
    }
    .creditCardForm .owner {
        width: 63%;
        margin-right: 10px;
    }
    .creditCardForm .CVV {
        width: 35%;
    }
    .creditCardForm #card-number-field {
        width: 100%;
    }
    .creditCardForm #expiration-date {
        width: 49%;
    }
    .creditCardForm #credit_cards {
        width: 50%;
        margin-top: 25px;
        text-align: right;
    }
    .creditCardForm #pay-now {
        width: 100%;
        margin-top: 25px;
    }
    .creditCardForm .payment .btn {
        width: 100%;
        margin-top: 3px;
        font-size: 24px;
        background-color: #2ec4a5;
        color: white;
    }
    .creditCardForm .payment select {
        padding: 10px;
        margin-right: 15px;
    }
    @media(max-width: 650px) {
        .creditCardForm .owner,
        .creditCardForm .CVV,
        .creditCardForm #expiration-date,
        .creditCardForm #credit_cards {
            width: 100%;
        }
        .creditCardForm #credit_cards {
            text-align: left;
        }
    }
</style>
