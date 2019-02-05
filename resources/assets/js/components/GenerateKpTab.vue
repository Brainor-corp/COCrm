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
                <input type="text" :name="'offer_group[name]'" v-model="offerGroup.name"/>
                <ul class="nav nav-tabs">
                    <li v-for="offerTab in offersTabs" class="nav-item">
                        <a class="nav-link" data-toggle="tab" :href="'#kp-edit-tab-'+offerTab.id">
                            <!--<input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][id]'" v-model="offerTab.id"/>-->
                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][name]'" v-model="offerTab.name"/><br>
                            <label :for="'tax-select-'+offerTab.id">Налог</label>
                            <select :id="'tax-select-'+offerTab.id" :name="'offer_group[offers]['+offerTab.id+'][tax]'" v-model="offerTab.tax">
                                <option v-for="tax in taxes" :value="tax.value">{{ tax.value }}</option>
                            </select>
                            <span @click="deleteOffer(offerTab.id)">X</span>
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
                                    <!--<input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+'][id]'" v-model="offerContentTab.id"/>-->
                                    <!--<input type="hidden" hidden="hidden" v-if="types[selected[offerTab.id][offerContentTab.id]]" :name="'offer_group[offers]['+offerTab.id+'][equipments][`'+types[selected[offerTab.id][offerContentTab.id]][0].id+'`][type_name]'" v-model="types[selected[offerTab.id][offerContentTab.id]][0].name"/>-->
                                    <select v-model="selected[offerTab.id][offerContentTab.id][0]">
                                        <option disabled value="new">Выберите</option>
                                        <option v-for="type in types" :value="type[0].id">{{ type[0].name }}</option>
                                    </select>
                                    <span @click="deleteTab(offerTab.id,offerContentTab.id)">X</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" @click.prevent="addOfferContentTab(offerTab.id)">+</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div v-for="offerContentTab in offersContentTabs[offerTab.id]" :id="'kp-'+offerTab.id+'-content-edit-tab-'+offerContentTab.id" class="tab-pane fade">
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">Артикул</th>
                                        <th scope="col">Название</th>
                                        <th scope="col">Ед.измерения</th>
                                        <th scope="col">Количество</th>
                                        <th scope="col">Цена</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in offerContentTab.rows">
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][base_id]'" v-model="row.base_id"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][description]'" v-model="row.description"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][price_trade]'" v-model="row.price_trade"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][price_special]'" v-model="row.price_special"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][comment]'" v-model="row.comment"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][type_id]'" v-model="row.type_id"/>
                                        <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][type]'" v-model="row.type"/>
                                        <td>
                                            <input type="text" @keyup="searchEquipmentByCode(row.code, row.id)" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][code]'" v-model="row.code" />
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
                                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][name]'" v-model="row.name"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][points]'" v-model="row.points"/>
                                        </td>
                                        <td>
                                            <input type="number" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][quantity]'" v-model="row.quantity"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+row.class+']['+offerContentTab.id+']['+row.id+'][price]'" v-model="row.price"/>
                                        </td>
                                        <td>
                                            <span @click="deleteRow(offerTab.id,offerContentTab.id, row.id)">X</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" @click.prevent="addTableRow(offerTab.id,offerContentTab.id)">+</td>
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
                offersContentTabs:[[]],
                offerGroup:{name:'Шаблон КП'},
                results: [],
                groupId: null,
                autocompletesDisplays: [],
                types: {'new' : [{id:'new'}]},
                taxes: [],
                defaultTypes:[],
                selected: {
                    0:[{
                        0: 'new'
                    }]
                },
                search: "",
                isLoading: false,
                redactMode: false,
                searchResult: [],
            };
        },
        components: {Autocomplete},
        computed: {
        },
        beforeCreate: function(){

            axios.post(window.location.href + 'getTaxBySlug')
                .then((res) => {
                    this.taxes = res.data;

                    return axios.post(window.location.href + 'getAllEquipmentTypes');
                })
                .then((res) => {
                    this.types = res.data;
                    this.types['new'] = [{'id': 1}];

                    return axios.post(window.location.href + 'getDefaultTypesWithEquipment');
                })
                .then((res) => {
                    this.defaultTypes = res.data;
                    this.offersContentTabs[0].push({
                        'id': 0,
                        'name': 'Новая ',
                        'rows': [],
                    });
                    for(let i = 0 ; i < res.data.length ; i++) {
                        this.offersContentTabs[0].push({
                            'id': i+1,
                            'name': res.data[i]['name'],
                            'rows': [],
                        });
                        this.selected[0].push( [res.data[i]['id']] );
                        if(res.data[i]['equipment'].length > 0){
                            for (let j = 0; j < res.data[i]['equipment'].length; j++) {
                                this.offersContentTabs[0][i+1]['rows'].push({
                                    'id': j,
                                    'saveType': 'old',
                                    'base_id':  res.data[i]['equipment'][j].id,
                                    'name': res.data[i]['equipment'][j].name,
                                    'quantity': res.data[i]['equipment'][j].pivot.quantity,
                                    'code': res.data[i]['equipment'][j].code,
                                    'type': this.types[res.data[i]['equipment'][j].type_id][0].slug,
                                    // 'price': res.data[i]['equipment'][j].pivot.price,
                                    // 'price_trade': res.data[i]['equipment'][j].pivot.price_trade,
                                    // 'price_small_trade': res.data[i]['equipment'][j].pivot.price_small_trade,
                                    // 'price_special': res.data[i]['equipment'][j].pivot.price_special,
                                    // 'comment': res.data[i]['equipment'][j].pivot.comment,
                                    'description': res.data[i]['equipment'][j].description,
                                    'class': res.data[i]['equipment'][j].class,
                                });
                            }
                        }
                    }
                });

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
                lastOfferTab = this.offersTabs[this.offersTabs.length - 1];
                lastOfferTabId = lastOfferTab ? lastOfferTab.id+1 : 0;
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
                this.selected[lastOfferTabId] = {
                    0: 'new'
                };
            },
            addOfferContentTab(offerTabId){
                let lastOfferContentTab;
                let lastOfferContentTabId;
                lastOfferContentTab =  this.offersContentTabs[offerTabId][this.offersContentTabs[offerTabId].length - 1];
                lastOfferContentTabId = lastOfferContentTab ? lastOfferContentTab.id+1 : 0;
                this.offersContentTabs[offerTabId].push(
                    {
                        id: lastOfferContentTabId,
                        name:'Новое оборудование',
                        rows:[]
                    }
                );
                this.selected[offerTabId][lastOfferContentTab.id+1] = 'new';
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
                        base_id: -1,
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
                        class: this.types[this.selected[offerTabId][offerContentTabId][0]][0].class,
                        type: this.types[this.selected[offerTabId][offerContentTabId][0]][0].slug,
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
                this.autocompletesDisplays[rowId] = true;
            },
            setResult(equipment, offerTabId, offerContentTabId, rowId) {
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['id'] = rowId;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['saveType'] = 'old';
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['code'] = equipment.code;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['name'] = equipment.name;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['description'] = equipment.description;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['quantity'] = 1;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['points'] = equipment.points;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price'] = equipment.price;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_trade'] = equipment.price_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_small_trade'] = equipment.price_small_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_special'] = equipment.price_special;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['comment'] = equipment.comment;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['type_id'] = equipment.type.id;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['type'] = equipment.type.name;
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
                let buffGroupName = '';
                let buffSelected = [];
                axios
                    .post(window.location.href + 'getOfferGroup', {
                        id: this.groupId
                    })
                    .then(resp => {
                        buffGroupName = resp.data.name;
                        resp.data.offers.forEach(function (offer) {
                            let lastOfferContentTabId = 0;
                            buffKP.push(
                                {
                                    id: lastOfferTabId,
                                    name: offer.name,
                                    tax: offer.tax
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
                                            base_id: equipment.id,
                                            code: equipment.code,
                                            name: equipment.name,
                                            description: equipment.description,
                                            quantity: equipment.pivot.quantity,
                                            points: equipment.points,
                                            price: equipment.pivot.price,
                                            price_trade: equipment.pivot.price_trade,
                                            price_small_trade: equipment.pivot.price_small_trade,
                                            price_special: equipment.pivot.price_special,
                                            comment: equipment.pivot.comment,
                                            type_id: equipment.type.id,
                                            type: equipment.type.slug,
                                            class: equipment.type.class,
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
                                if(!buffSelected[lastOfferTabId]){
                                    buffSelected[lastOfferTabId] = [];
                                }
                                if(!buffSelected[lastOfferTabId][lastOfferContentTabId]){
                                    buffSelected[lastOfferTabId][lastOfferContentTabId] = [];
                                }
                                buffEq[lastOfferTabId].push(
                                    {
                                        id: lastOfferContentTabId,
                                        name: type,
                                        rows: buffRow[lastOfferTabId][lastOfferContentTabId]['rows']
                                    }
                                );
                                buffSelected[lastOfferTabId][lastOfferContentTabId][0] = buffRow[lastOfferTabId][lastOfferContentTabId]['rows'][0]['type_id'];
                                lastOfferContentTabId++;
                            });

                            lastOfferTabId++;
                        });
                        this.offerGroup.name = buffGroupName;
                        this.offersTabs = buffKP;
                        this.offersContentTabs = buffEq;
                        this.selected = buffSelected;
                    });
            },
            deleteRow(offerTabId, offerContentTabId, rowId){
                for(let i = 0; i < this.offersContentTabs[offerTabId].length; i++){
                    if(this.offersContentTabs[offerTabId][i].id === offerContentTabId){
                        for(let j = 0; j < this.offersContentTabs[offerTabId][i]['rows'].length; j++) {
                            if(this.offersContentTabs[offerTabId][i]['rows'][j].id === rowId){
                                this.offersContentTabs[offerTabId][i]['rows'].splice(j, 1);
                                break;
                            }
                        }
                    }
                }
            },
            deleteTab(offerTabId, offerContentTabId){
                for(let i = 0; i < this.offersContentTabs[offerTabId].length; i++){
                    if(this.offersContentTabs[offerTabId][i].id === offerContentTabId){
                        this.offersContentTabs[offerTabId].splice(i, 1);
                        break;
                    }
                }
            },
            deleteOffer(offerTabId){
                for(let i = 0; i < this.offersTabs.length; i++){
                    if(this.offersTabs[i].id === offerTabId){
                        this.offersTabs.splice(i, 1);
                        this.offersContentTabs.splice(i, 1);
                        break;
                    }
                }
            }
        }
    }
</script>
