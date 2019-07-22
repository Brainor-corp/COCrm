<template>
    <div class="container main-container my-2">
        <div class="row">
            <div class="col-12 first-line-tabs">
                <ul class="upper-tabs nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generateKpTab">Редактирование КП</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kpTotalTab" @click="updateOfferGroup() + calculatePrices()">Итог</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="generateKpTab" class="active bg-white border fade p-3 show tab-pane">
                        <generate-kp-tab :offerGroupID="offerGroupID"
                                         @updateOfferGroup="updateOfferGroup"
                        ></generate-kp-tab>
                    </div>
                    <div id="kpTotalTab" class="bg-white border fade p-3 tab-pane">
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
    require('nprogress/nprogress.css');
    require('nprogress/nprogress');

    export default {
        components: { GenerateKpTab, KpTotalTab },
        data(){
            return {
                offerGroup: [],
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
                        $.each(this.offerGroup['offer_group']['offers'], (offerIndex, offer) => {
                            if(this.offerGroup['offer_group']['offers'][offerIndex]['equipments']){
                                $.each(offer['equipments'], (type, equipments) => {
                                    if(!this.offerGroup['offer_group']['offers'][offerIndex]['equipments'][type]){
                                        this.offerGroup['offer_group']['offers'][offerIndex]['equipments'].splice(type, 1);
                                        stop = false;
                                    }
                                    $.each(equipments['equipment'], (index, equipment) => {
                                        if(
                                            !equipment
                                            || equipment.quantity === ""
                                        // || parseInt(equipment.quantity) < 1
                                        ) { //проверка на ненулевое кол-во
                                            equipments['equipment'].splice(index, 1);
                                            stop = false;
                                        }
                                    });
                                    // if(stop){
                                    //     $.each(equipments['equipment'], (index, equipment) => {
                                    //         // this.offerGroup['offer_group']['offers'][offer]['equipments'][type][index]['price'] = (((equipment['price_small_trade'] - equipment['price_special'])/2) + equipment['price_special']) * equipment['quantity'];
                                    //         equipment['counted_price'] = parseFloat((parseFloat(parseFloat(equipment['price_small_trade']) - parseFloat(equipment['price_special'])/2) + parseFloat(equipment['price_special']))).toFixed(2);
                                    //     });
                                    // }
                                });
                            }
                        });
                        if(this.offerGroup['offer_group']['works']){
                            $.each(this.offerGroup['offer_group']['works'], (workTabIndex, workTab) => {
                                $.each(workTab['work'], (workIndex, work) => {
                                    if(work) {
                                        if (
                                            work.quantity === ""
                                            || work.quantity == null
                                        // || work.quantity < 1
                                        ) {
                                            this.offerGroup['offer_group']['works'][workTabIndex]['work'].splice(workIndex, 1);
                                            stop = false;
                                        }
                                    }
                                });
                            });
                        }
                        if(stop){
                            break;
                        }
                    }
                    // console.log(this.offerGroup);
                }
            },
            calculatePrices(){
                this.calcPrices = [];
                let empty = true;
                $.each(this.offerGroup['offer_group']['offers'], (offerIndex, offer) => {
                    $.each(offer['equipments'], (equipmentTypeIndex, equipments) => {
                        if(equipments['equipment'].length > 0){
                            empty = false;
                        }
                    });
                });
                if(!empty) {
                    axios
                        .post('/calculateAllPrices',
                            this.offerGroup
                        )
                        .then(res => {
                            console.log(this.offerGroup);
                            console.log(res.data);
                            this.calcPrices = res.data;
                        })
                        .catch(error => {
                            NProgress.done();
                            console.log(error.response.data.message);
                        });
                }
            }
        }
    }
</script>