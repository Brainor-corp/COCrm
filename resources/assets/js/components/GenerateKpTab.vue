<template>
    <div class="row">
        <div class="col-12 generate-kp-tab">
            <h3>Редактирование КП</h3>
            <form>
                <ul class="nav nav-tabs">
                    <li v-for="offerTab in offersTabs" class="nav-item">
                        <a class="nav-link" data-toggle="tab" :href="'#kp-edit-tab-'+offerTab.id">
                            <input type="text" v-model="offerTab.name"/>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" @click.prevent="addOfferTab">+</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div v-for="offerTab in offersTabs" :id="'kp-edit-tab-'+offerTab.id" class="tab-pane fade">
                        <ul class="nav nav-tabs">
                            <li v-for="offerContentTab in offersContentTabs[offerTab.id]" class="nav-item">
                                <a class="nav-link" data-toggle="tab" :href="'#kp-'+offerTab.id+'-content-edit-tab-'+offerContentTab.id">
                                    <input type="text" v-model="offerContentTab.name"/>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" @click.prevent="addOfferContentTab(offerTab.id)">+</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div v-for="offerContentTab in offersContentTabs[offerTab.id]" :id="'kp-'+offerTab.id+'-content-edit-tab-'+offerContentTab.id" class="tab-pane fade">
                                {{ offerContentTab.name}}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                offersTabs:[
                    { id: 0, name:'Новый тип КП' },
                ],
                offersContentTabs:[[{id: 0, name:'Новое оборудование'}]],
                offerGroup:[
                    { message: 'Foo' },
                    { message: 'Bar' }
                ]
            };
        },
        computed: {
        },
        created: function (){
            this.$emit( 'updateOfferGroup', this.offerGroup );
        },
        methods: {
            updateOfferGroup(newOfferGroup) {
                this.offerGroup = newOfferGroup;
            },
            addOfferTab(){
                let lastOfferTab,lastOfferTabId;
                lastOfferTab =  this.offersTabs[this.offersTabs.length - 1];
                lastOfferTabId = lastOfferTab.id+1;
                this.offersTabs.push(
                    {

                        id: lastOfferTabId,
                        name:'Новый тип КП'
                    }
                );
                this.offersContentTabs.push([]);
                this.offersContentTabs[lastOfferTabId].push(
                    {
                        id: 0,
                        name:'Новое оборудование'
                    }
                );
                console.log(this.offersContentTabs);
            },
            addOfferContentTab(offerTabId){
                let lastOfferContentTab;
                lastOfferContentTab =  this.offersContentTabs[offerTabId][this.offersContentTabs[offerTabId].length - 1];
                this.offersContentTabs[offerTabId].push(
                    {
                        id: lastOfferContentTab.id+1,
                        name:'Новое оборудование'
                    }
                );
            }
        }
    }
</script>
