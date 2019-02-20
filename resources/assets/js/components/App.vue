<template>
    <div class="container main-container">
        <div class="row">
            <div class="col-12 first-line-tabs">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generateKpTab">Редактирование КП</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kpTotalTab" @click="updateOfferGroup() + calculatePrices()">Итог</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="generateKpTab" class="tab-pane fade active show">
                        <generate-kp-tab :offerGroupID="offerGroupID"
                            @updateOfferGroup="updateOfferGroup"
                        ></generate-kp-tab>
                    </div>
                    <div id="kpTotalTab" class="tab-pane fade">
                        <kp-total-tab :offerGroup="offerGroup" :offerGroupID="offerGroupID" :calcPrices="calcPrices"></kp-total-tab>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import GenerateKpTab from './GenerateKpTab';
    import KpTotalTab from './KpTotalTab';
    import deparam from 'deparam';
    import axios from 'axios';
    import $ from 'jquery';

    export default {
        components: { GenerateKpTab, KpTotalTab },
        data(){
            return {
                offerGroup:[],
                calcPrices: [],
                offerGroupID: null,
            };
        },
        computed: {
            title() {
                return this.$store.state.title.title;
            }
        },
        beforeCreate: function(){
        },
        created: function (){
            this.offerGroupID = new URL(window.location.href).searchParams.get("id");

        },
        methods: {
            updateOfferGroup(newOfferGroup) {
                if (typeof newOfferGroup !== 'undefined') {
                    this.offerGroup = newOfferGroup;
                    this.calcPrices = [];
                }else{
                    this.offerGroup = deparam($('#kp-generate-form').serialize());
                    while(true) {
                        var stop = true;
                        for (let offer = 0; offer < this.offerGroup['offer_group']['offers'].length; offer++) {
                            if(this.offerGroup['offer_group']['offers'][offer]['equipments']){
                                if(this.offerGroup['offer_group']['offers'][offer]['equipments']){
                                    $.each(this.offerGroup['offer_group']['offers'][offer]['equipments'], (type, equipments) => {
                                        if(!this.offerGroup['offer_group']['offers'][offer]['equipments'][type]){
                                            this.offerGroup['offer_group']['offers'][offer]['equipments'].splice(type, 1);
                                            stop = false;
                                        }
                                        $.each(equipments, (index, equipment) => {
                                            if(!equipment || equipment.quantity === "" || parseInt(equipment.quantity) < 1) { //проверка на ненулевое кол-во
                                                this.offerGroup['offer_group']['offers'][offer]['equipments'][type].splice(index, 1);
                                                stop = false;
                                            }
                                        });
                                        if(stop){
                                            $.each(equipments, (index, equipment) => {
                                                // this.offerGroup['offer_group']['offers'][offer]['equipments'][type][index]['price'] = (((equipment['price_small_trade'] - equipment['price_special'])/2) + equipment['price_special']) * equipment['quantity'];
                                                this.offerGroup['offer_group']['offers'][offer]['equipments'][type][index]['counted_price'] = Math.floor(parseFloat(equipment['price_small_trade']) + parseFloat(equipment['price_special']))/2;
                                            });
                                        }
                                    });
                                }
                            }
                        }
                        if(this.offerGroup['offer_group']['works']){
                            for (let work = 0; work < this.offerGroup['offer_group']['works'].length; work++){
                                if(this.offerGroup['offer_group']['works'][work].quantity === "" || this.offerGroup['offer_group']['works'][work].quantity < 1){
                                    this.offerGroup['offer_group']['works'].splice(work, 1);
                                    stop = false;
                                }
                            }
                        }
                        if(stop)
                            break;
                    }

                }
            },
            calculatePrices(){
                axios
                    .post('/calculateAllPrices',
                         this.offerGroup
                    )
                    .then(res=>{
                        this.calcPrices = res.data;
                    });
            }
        }
    }
</script>
