<template>
    <div class="row">
        <div class="col-12 my-5">
            <form id="getOfferGroup" @submit.prevent="getOfferGroup">
                <input type="text" v-bind:disabled="redactMode" placeholder="id КП" v-model="groupId">
                <button type="submit" v-bind:disabled="redactMode">Вставить КП</button>
            </form>
        </div>
        <div class="col-12 generate-kp-tab">
            <h3>Редактирование КП</h3>
            <form id="kp-generate-form" @change.prevent="updateOfferGroup">
                <ul class="nav nav-tabs">
                    <li v-for="offerTab in offersTabs" class="nav-item">
                        <a class="nav-link" data-toggle="tab" :href="'#kp-edit-tab-'+offerTab.id">
                            <input type="hidden" hidden="hidden" :name="'offer_group['+offerTab.id+'][id]'" v-model="offerTab.id"/>
                            <input type="text" :name="'offer_group['+offerTab.id+'][name]'" v-model="offerTab.name"/>
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
                                    <input type="hidden" hidden="hidden" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][id]'" v-model="offerContentTab.id"/>
                                    <input type="text" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][name]'" v-model="offerContentTab.name"/>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" @click.prevent="addOfferContentTab(offerTab.id)">+</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div v-for="offerContentTab in offersContentTabs[offerTab.id]" :id="'kp-'+offerTab.id+'-content-edit-tab-'+offerContentTab.id" class="tab-pane fade">
                                {{ offerContentTab.name}}
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">Артикул</th>
                                        <th scope="col">Название</th>
                                        <th scope="col">Ед.измерения</th>
                                        <th scope="col">Количество</th>
                                        <th scope="col">Цена</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in offerContentTab.rows">
                                        <td>
                                            <input type="text" @keyup="searchEquipmentByCode(row.code, row.id)" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][rows]['+row.id+'][code]'" v-model="row.code" />
                                            <!--<autocomplete-->
                                                    <!--url="http://commercialoffer.local/findEquipmentByCode"-->
                                                    <!--anchor="code"-->
                                                    <!--:name="'autocomplete-' + row.id"-->
                                                    <!--param="code">-->
                                            <!--</autocomplete>-->
                                            <ul :id="'autocomplete-results-'+row.id" v-show="autocompletesDisplays[row.id]" class="autocomplete-results">
                                                <li class="loading" v-if="isLoading">
                                                    Поиск...
                                                </li>
                                                <li v-else v-for="(result, i) in results" :key="i" @click="setResult(result, offerTab.id, offerContentTab.id, row.id)" class="autocomplete-result">
                                                    {{ result['code'] }}
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][rows]['+row.id+'][name]'" v-model="row.name"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][rows]['+row.id+'][points]'" v-model="row.points"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][rows]['+row.id+'][quantity]'" v-model="row.quantity"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group['+offerTab.id+'][offers]['+offerContentTab.id+'][rows]['+row.id+'][price]'" v-model="row.price"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" @click.prevent="addTableRow(offerTab.id,offerContentTab.id)">+</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import $ from 'jquery';
    import deparam from 'deparam';
    import axios from 'axios';
    import Autocomplete from 'vue2-autocomplete-js'
    require("../../../../node_modules/vue2-autocomplete-js/dist/style/vue2-autocomplete.css");

    export default {
        data(){
            return {
                offersTabs:[
                    { id: 0, name:'Новый тип КП' },
                ],
                offersContentTabs:[[{id: 0, name:'Новое оборудование', rows:[]}]],
                offerGroup:[],
                results: [],
                groupId: null,
                autocompletesDisplays: [],
                search: "",
                isLoading: false,
                redactMode: false,
                searchResult: [],
            };
        },
        components: {Autocomplete},
        computed: {
        },
        created: function (){
            this.updateOfferGroup;
        },
        mounted() {
            document.addEventListener("click", this.handleClickOutside);
        },
        destroyed() {
            document.removeEventListener("click", this.handleClickOutside);
        },
        methods: {
            updateOfferGroup() {
                this.$emit('updateOfferGroup');
                this.redactMode = true;
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
                        name:'Новое оборудование',
                        rows:[]
                    }
                );
            },
            addOfferContentTab(offerTabId){
                let lastOfferContentTab;
                lastOfferContentTab =  this.offersContentTabs[offerTabId][this.offersContentTabs[offerTabId].length - 1];
                this.offersContentTabs[offerTabId].push(
                    {
                        id: lastOfferContentTab.id+1,
                        name:'Новое оборудование',
                        rows:[]
                    }
                );
            },
            addTableRow(offerTabId,offerContentTabId){
                let lastRow;

                if(this.offersContentTabs[offerTabId][offerContentTabId]['rows'].length > 0){
                    lastRow =  this.offersContentTabs[offerTabId][offerContentTabId]['rows'][this.offersContentTabs[offerTabId][offerContentTabId]['rows'].length - 1].id+1;
                }else{
                    lastRow = 0;
                }

                this.autocompletesDisplays.push({lastRow: false});

                this.offersContentTabs[offerTabId][offerContentTabId]['rows'].push(
                    {
                        id: lastRow,
                        saveType: 'new',
                        code: '',
                        name:'',
                        description:'',
                        quantity:'',
                        points:'',
                        price:'',
                        price_trade:'',
                        price_small_trade:'',
                        price_special:'',
                        comment:'',
                    }
                );
            },
            searchEquipmentByCode(codePart, rowId){
                let context = this;
                axios.interceptors.request.use(function (config) {
                    context.isLoading = true;
                    return config;
                });

                axios
                    .post(window.location.href + 'findEquipmentByCode', {
                        code: codePart
                    })
                    .then(resp => {
                        this.results = resp.data;
                        this.isLoading = false;
                    });
                console.log(rowId);
                this.autocompletesDisplays[rowId] = true;
                console.log(this.autocompletesDisplays);
            },
            setResult(equipment, offerTabId, offerContentTabId, rowId) {
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['id'] = rowId;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['saveType'] = 'old';
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['code'] = equipment.code;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['name'] = equipment.name;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['description'] = equipment.description;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['quantity'] = equipment.naquantityme;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['points'] = equipment.points;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price'] = equipment.price;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_trade'] = equipment.price_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_small_trade'] = equipment.price_small_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_special'] = equipment.price_special;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['comment'] = '';
                // this.row.code = result;
                this.autocompletesDisplays[rowId] = false;
            },
            handleClickOutside(event) {
                if ('autocomplete-results' !== $(event.target).attr('class')) {
                    let i, length = this.autocompletesDisplays.length;
                    for (i = 0; i < length; i = i + 1) {
                        Vue.set(this.autocompletesDisplays, i, false)
                    }
                }
            },
            getOfferGroup() {
                let lastOfferTabId = 0;
                let buffKP = [];
                let buffEq = [];
                let buffRow = [];
                axios
                    .post(window.location.href + 'getOfferGroup', {
                        id: this.groupId
                    })
                    .then(resp => {
                        resp.data.offers.forEach(function (offer) {
                            let lastOfferContentTabId = 0;
                            buffKP.push(
                                {
                                    id: lastOfferTabId,
                                    name: offer.name
                                }
                            );

                            Object.keys(offer['equipments']).forEach(function (type) {
                                let lastRow = 0;
                                offer['equipments'][type].forEach(function (equipment) {
                                    if(!buffRow[lastOfferTabId]){
                                        buffRow[lastOfferTabId] = [];
                                    }
                                    if(!buffRow[lastOfferTabId][lastOfferContentTabId]){
                                        buffRow[lastOfferTabId][lastOfferContentTabId] = [];
                                    }
                                    if(!buffRow[lastOfferTabId][lastOfferContentTabId]['rows']){
                                        buffRow[lastOfferTabId][lastOfferContentTabId]['rows'] = [];
                                    }
                                    buffRow[lastOfferTabId][lastOfferContentTabId]['rows'].push(
                                        {
                                            id: lastRow,
                                            saveType: 'old',
                                            code: equipment.code,
                                            name: equipment.name,
                                            description: equipment.description,
                                            quantity: equipment.pivot.quantity,
                                            points: equipment.points,
                                            price: equipment.pivot.price,
                                            price_trade: equipment.pivot.code,
                                            price_small_trade: equipment.pivot.code,
                                            price_special: equipment.pivot.code,
                                            comment:'',
                                        }
                                    );
                                    lastRow++;
                                });

                                if(!buffEq[lastOfferTabId]){
                                    buffEq[lastOfferTabId] = [];
                                }
                                if(!buffRow[lastOfferTabId][lastOfferContentTabId]){
                                    buffRow[lastOfferTabId][lastOfferContentTabId] = [];
                                }
                                buffEq[lastOfferTabId].push(
                                    {
                                        id: lastOfferContentTabId,
                                        name: type,
                                        rows: buffRow[lastOfferTabId][lastOfferContentTabId]['rows']
                                    }
                                );
                                lastOfferContentTabId++;
                            });

                            lastOfferTabId++;
                        });
                        this.offersTabs = buffKP;
                        this.offersContentTabs = buffEq;
                    });

            }
        }
    }
</script>
