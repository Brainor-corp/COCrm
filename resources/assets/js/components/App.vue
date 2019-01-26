<template>
    <div class="container">
        <div class="row">
            <div class="col-12 first-line-tabs">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generateKpTab">Редактирование КП</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kpTotalTab">Итог</a>
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
                this.offerGroup = newOfferGroup;
            }
        }
    }
</script>
