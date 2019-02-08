<template>
    <div class="container main-container">
        <div class="row">
            <div class="col-12 first-line-tabs">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generateKpTab">Редактирование КП</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kpTotalTab" @click="updateOfferGroup()">Итог</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="generateKpTab" class="tab-pane fade active show">
                        <generate-kp-tab
                            @updateOfferGroup="updateOfferGroup"
                        ></generate-kp-tab>
                    </div>
                    <div id="kpTotalTab" class="tab-pane fade">
                        <kp-total-tab :offerGroup="offerGroup" :consumablePrice="consumablePrice"></kp-total-tab>
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
    import $ from 'jquery';

    export default {
        components: { GenerateKpTab, KpTotalTab },
        data(){
            return {
                offerGroup:[],
                consumablePrice: 0,
            };
        },
        computed: {
            title() {
                return this.$store.state.title.title;
            }
        },
        created: function (){
        },
        methods: {
            updateOfferGroup(newOfferGroup) {
                if (typeof newOfferGroup !== 'undefined') {
                    this.offerGroup = newOfferGroup;
                }else{
                    this.consumablePrice = 0;
                    this.offerGroup = deparam($('#kp-generate-form').serialize());
                    while(true) {
                        var stop = true;
                        for (let offer = 0; offer < this.offerGroup['offer_group']['offers'].length; offer++) {
                            if(this.offerGroup['offer_group']['offers'][offer]['equipments']){
                                if(this.offerGroup['offer_group']['offers'][offer]['equipments']){
                                    for (let equipment_tab = 0; equipment_tab < this.offerGroup['offer_group']['offers'][offer]['equipments'].length; equipment_tab++) {
                                        if(this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab] == null){
                                            this.offerGroup['offer_group']['offers'][offer]['equipments'].splice(equipment_tab, 1);
                                            stop = false;
                                            break;
                                        }
                                        for (let equipment = 0; equipment < this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab].length; equipment++) {
                                            if(this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab][equipment].quantity === "" || parseInt(this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab][equipment].quantity) < 1) { //проверка на ненулевое кол-во
                                                this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab].splice(equipment, 1);
                                                stop = false;
                                                break;
                                            }
                                            if(this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab][equipment].type === 'rashodnye-materialy'){
                                                this.consumablePrice += this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab][equipment].quantity * this.offerGroup['offer_group']['offers'][offer]['equipments'][equipment_tab][equipment].price;
                                            }
                                        }
                                    }
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
        }
    }
</script>
