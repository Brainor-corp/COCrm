<template>
    <div class="container">
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
                        <kp-total-tab :offerGroup="offerGroup"></kp-total-tab>
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
                offerGroup:[]
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
                    this.offerGroup = deparam($('#kp-generate-form').serialize());
                    console.log(this.offerGroup['offer_group']);
                    while(true) {
                        var stop = true;
                        for (let offer = 0; offer < this.offerGroup['offer_group']['offers'].length; offer++) {
                            for (let equipment_tab = 0; equipment_tab < this.offerGroup['offer_group']['offers'][offer]['equipments']['equipment'].length; equipment_tab++) {
                                for (let equipment = 0; equipment < this.offerGroup['offer_group']['offers'][offer]['equipments']['equipment'][equipment_tab].length; equipment++) {
                                    if (this.offerGroup['offer_group']['offers'][offer]['equipments']['equipment'][equipment_tab][equipment].quantity === "" || this.offerGroup['offer_group']['offers'][offer]['equipments']['equipment'][equipment_tab][equipment].quantity < 1) {
                                        this.offerGroup['offer_group']['offers'][offer]['equipments']['equipment'][equipment_tab].splice(equipment, 1);
                                        stop = false;
                                        console.log('work');//todo остановился здесь
                                        break;
                                    }
                                }
                            }
                            for (let work = 0; work < this.offerGroup['offer_group']['offers'][offer]['equipments'].work.length; work++) {

                            }
                        }
                        if(stop)
                            break;
                    }
                }
            }
        }
    }
</script>
