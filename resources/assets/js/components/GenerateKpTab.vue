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
                <input class="my-3" type="text" :name="'offer_group[name]'" v-model="offerGroup.name"/>
                <h3>Оборудование</h3>
                <ul class="nav nav-tabs">
                    <li v-for="offerTab in offersTabs" class="nav-item">
                        <a class="nav-link" data-toggle="tab" :href="'#kp-edit-tab-'+offerTab.id">
                            <div class="row">
                                <div class="col-10">
                                    <input type="text" :name="'offer_group[offers]['+offerTab.id+'][name]'" v-model="offerTab.name"/><br>
                                </div>
                                <div class="col-2">
                                    <span @click="deleteOffer(offerTab.id)">X</span>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" @click.prevent="addOfferTab">+</a>
                    </li>
                </ul>

                <div class="tab-content mb-5">
                    <div v-for="offerTab in offersTabs" :id="'kp-edit-tab-'+offerTab.id" class="tab-pane fade">
                        <ul class="nav nav-tabs">
                            <li v-for="offerContentTab in offersContentTabs[offerTab.id]" class="nav-item">
                                <a class="nav-link" data-toggle="tab" :href="'#kp-'+offerTab.id+'-content-edit-tab-'+offerContentTab.id">
                                    <select class="type-select" :data-toggle="'tooltip-'+offerTab.id+'-'+offerContentTab.id" title="Сначала выберите тип" v-model="selected[offerTab.id][offerContentTab.id]">
                                        <option disabled value="new">Выберите</option>
                                        <option v-for="type in types" v-if="type[0]!='new'" :value="type[0].id">{{ type[0].name }}</option>
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
                                        <th scope="col">Розн. цена</th>
                                        <th scope="col">Мин. розн. цена</th>
                                        <th scope="col">Спец. цена</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in offerContentTab.rows">
                                        <td>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][base_id]'" v-model="row.base_id"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][description]'" v-model="row.description"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_trade]'" v-model="row.price_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_special]'" v-model="row.price_special"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][comment]'" v-model="row.comment"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][type]'" v-model="row.type"/>

                                            <input type="text" @keyup="searchEquipmentByCode(row.code, offerTab.id, offerContentTab.id, row.id)" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][code]'" v-model="row.code" />
                                            <ul :id="'autocomplete-results-e-'+offerTab.id+'-'+offerContentTab.id+'-'+row.id" v-show="autocompletesDisplays['equipments'][offerTab.id][offerContentTab.id][row.id]" class="autocomplete-results">
                                                <li class="loading" v-if="isLoading">
                                                    Поиск...
                                                </li>
                                                <li v-else v-for="(result, i) in results" :key="i" @click="setResult(result, offerTab.id, offerContentTab.id, row.id)" class="autocomplete-result">
                                                    {{ result['code'] }}
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][name]'" v-model="row.name"/>
                                        </td>
                                        <td>
                                            <input type="text" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][points]'" v-model="row.points"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][quantity]'" v-model="row.quantity"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price]'" v-model="row.price"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_trade]'" v-model="row.price_trade"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" :name="'offer_group[offers]['+offerTab.id+'][equipments]['+offerContentTab.id+']['+row.id+'][price_special]'" v-model="row.price_special"/>
                                        </td>
                                        <td>
                                            <span @click="deleteRow(offerTab.id,offerContentTab.id, row.id)">X</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" v-bind:disabled="selected[offerTab.id][offerContentTab.id] === 'new'" @click.prevent="addTableRow(offerTab.id,offerContentTab.id)">+</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Работы</h3>
                <div class="row">
                    <div class="col-auto">
                        <label for="adjusters-number">Кол-во монтажников</label><br>
                        <input type="number" min="0" id="adjusters-number" v-model="adjusters['number']" :name="'offer_group[adjusters][adjusters_number]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-days">Дней работы</label><br>
                        <input type="number" min="0" id="adjusters-days" v-model="adjusters['days']" :name="'offer_group[adjusters][adjustment_days]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-fuel">Топливо</label><br>
                        <input type="number" step="50" min="0" id="adjusters-fuel" v-model="adjusters['fuel']" :name="'offer_group[adjusters][fuel]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-wage">Ставка</label><br>
                        <input type="number" min="0" step="100" id="adjusters-wage" v-model="adjusters['wage']" :name="'offer_group[adjusters][adjusters_wage]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-wage">Процент монтажникам</label><br>
                        <input type="number" min="0" max="100" id="adjusters-percent" v-model="adjusters['percentage']" :name="'offer_group[adjusters][pay_percentage]'"/>
                    </div>
                </div>
                <table class="table table-striped table-hover table-bordered my-3">
                    <thead>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название</th>
                        <th scope="col">Ед.измерения</th>
                        <th scope="col">Количество</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(work, key) in works" :key="key">
                        <td>
                            <input type="text" @keyup="searchWorkByCode(work.code, key)" :name="'offer_group[works]['+key+'][code]'" v-model="work.code"/>
                            <ul :id="'autocomplete-results-w'+key" v-show="autocompletesDisplays['works'][key]" class="autocomplete-results">
                                <li class="loading" v-if="isLoading">
                                    Поиск...
                                </li>
                                <li v-else v-for="(result, i) in results" :key="i" @click="setWorkResult(result, key)" class="autocomplete-result">
                                    {{ result['code'] }}
                                </li>
                            </ul>
                        </td>
                        <td>
                            <input type="text" :name="'offer_group[works]['+key+'][name]'" v-model="work.name"/>
                        </td>
                        <td>
                            <input type="text" :name="'offer_group[works]['+key+'][points]'" v-model="work.points"/>
                        </td>
                        <td>
                            <input type="number" min="0" :name="'offer_group[works]['+key+'][quantity]'" v-model="work.pivot.quantity"/>
                        </td>
                        <td>
                            <span @click="deleteWork(key)">X</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" @click.prevent="addWork()">+</td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</template>

<script>
    import $ from 'jquery';
    import deparam from 'deparam';
    import axios from 'axios';

    export default {
        data(){
            return {
                offersTabs:[
                    { id: 0, name:'Новый тип КП' },
                ],
                offersContentTabs:[[]],
                offerGroup:{name:'Шаблон КП'},
                works:[],
                adjusters:[],
                results: [],
                groupId: null,
                autocompletesDisplays: {
                    equipments: [],
                    works: []
                },
                types: [],
                defaultTypes:[],
                selected: [
                    []
                ],
                search: "",
                isLoading: false,
                redactMode: false,
                searchResult: [],
            };
        },
        components: {},
        computed: {
        },
        beforeCreate: function(){

            axios.post(window.location.href + 'getAllEquipmentTypes')
                .then((res) => {
                    this.types = res.data;
                    return axios.post(window.location.href + 'getDefaultTypesWithEquipment')
                })
                .then((res) => {
                    this.defaultTypes = res.data;
                    this.autocompletesDisplays['equipments'].push([]);
                    for(let i = 0 ; i < res.data.length ; i++) {
                        this.offersContentTabs[0].push({
                            'id': i,
                            'name': res.data[i]['name'],
                            'rows': [],
                        });
                        this.selected[0].push( res.data[i]['id'] );
                        this.autocompletesDisplays['equipments'][0].push([]);
                        if(res.data[i]['equipment'].length > 0){
                            for (let j = 0; j < res.data[i]['equipment'].length; j++) {
                                this.autocompletesDisplays['equipments'][0][j].push(false);
                                this.offersContentTabs[0][i]['rows'].push({
                                    'id': j,
                                    'saveType': 'old',
                                    'base_id':  res.data[i]['equipment'][j].id,
                                    'name': res.data[i]['equipment'][j].name,
                                    'quantity': res.data[i]['equipment'][j].pivot.quantity,
                                    'code': res.data[i]['equipment'][j].code,
                                    'type': this.types[res.data[i]['equipment'][j].type_id][0].slug,
                                    'price': res.data[i]['equipment'][j].pivot.price,
                                    'price_trade': res.data[i]['equipment'][j].pivot.price_trade,
                                    'price_small_trade': res.data[i]['equipment'][j].pivot.price_small_trade,
                                    'price_special': res.data[i]['equipment'][j].pivot.price_special,
                                    'comment': res.data[i]['equipment'][j].pivot.comment,
                                    'description': res.data[i]['equipment'][j].description,
                                    'points': res.data[i]['equipment'][j].points,
                                    'class': res.data[i]['equipment'][j].class,
                                });
                            }
                        }
                    }
                    console.log(this.autocompletesDisplays['equipments']);
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
            setDefaultTabs(index){
                axios
                    .post(window.location.href + 'getDefaultTypesWithEquipment')
                    .then(res => {
                        this.defaultTypes = res.data;
                        for(let i = 0 ; i < res.data.length ; i++) {
                            this.offersContentTabs[index].push({
                                'id': i,
                                'name': res.data[i]['name'],
                                'rows': [],
                            });
                            this.selected[index].push( res.data[i]['id'] );
                            this.autocompletesDisplays['equipments'][index].push([]);
                            if(res.data[i]['equipment'].length > 0){
                                for (let j = 0; j < res.data[i]['equipment'].length; j++) {
                                    this.autocompletesDisplays['equipments'][index][i].push(false);
                                    this.offersContentTabs[index][i]['rows'].push({
                                        'id': j,
                                        'saveType': 'old',
                                        'base_id':  res.data[i]['equipment'][j].id,
                                        'name': res.data[i]['equipment'][j].name,
                                        'quantity': res.data[i]['equipment'][j].pivot.quantity,
                                        'code': res.data[i]['equipment'][j].code,
                                        'type': this.types[res.data[i]['equipment'][j].type_id][0].slug,
                                        'price': res.data[i]['equipment'][j].pivot.price,
                                        'price_trade': res.data[i]['equipment'][j].pivot.price_trade,
                                        'price_small_trade': res.data[i]['equipment'][j].pivot.price_small_trade,
                                        'price_special': res.data[i]['equipment'][j].pivot.price_special,
                                        'comment': res.data[i]['equipment'][j].pivot.comment,
                                        'description': res.data[i]['equipment'][j].description,
                                        'points': res.data[i]['equipment'][j].points,
                                        'class': res.data[i]['equipment'][j].class,
                                    });
                                }
                            }
                        }
                    });
            },
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
                if(!this.selected[lastOfferTabId]){
                    this.selected[lastOfferTabId] = [];
                }
                this.setDefaultTabs(lastOfferTabId);
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
                this.selected[offerTabId][lastOfferContentTabId] = 'new';
            },
            addTableRow(offerTabId,offerContentTabId){
                if(this.selected[offerTabId][offerContentTabId] === 'new'){
                    $('[data-toggle="tooltip-'+offerTabId+'-'+offerContentTabId+'"]').tooltip({trigger: 'manual'});
                    $('[data-toggle="tooltip-'+offerTabId+'-'+offerContentTabId+'"]').tooltip('toggle');
                }
                else {
                    $('[data-toggle="tooltip-'+offerTabId+'-'+offerContentTabId+'"]').tooltip('hide');
                    let lastRow;

                    if (this.offersContentTabs[offerTabId][offerContentTabId]['rows'].length > 0) {
                        lastRow = this.offersContentTabs[offerTabId][offerContentTabId]['rows'][this.offersContentTabs[offerTabId][offerContentTabId]['rows'].length - 1].id + 1;
                    } else {
                        lastRow = 0;
                    }

                    this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId].push({lastRow: false});
                    this.offersContentTabs[offerTabId][offerContentTabId]['rows'].push(
                        {
                            id: lastRow,
                            saveType: 'new',
                            base_id: -1,
                            code: '',
                            name: '',
                            description: '',
                            quantity: '',
                            points: '',
                            price: '',
                            price_trade: '',
                            price_small_trade: '',
                            price_special: '',
                            comment: '',
                            class: this.types[this.selected[offerTabId][offerContentTabId]][0].class,
                            type: this.types[this.selected[offerTabId][offerContentTabId]][0].slug,
                        }
                    );
                }
            },
            searchEquipmentByCode(codePart, offerTabId, offerContentTabId, rowId){
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
                this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId][rowId] = true;
            },
            searchWorkByCode(codePart, rowId){
                let context = this;
                axios.interceptors.request.use(function (config) {
                    context.isLoading = true;
                    return config;
                });

                axios
                    .post(window.location.href + 'findWorkByCode', {
                        code: codePart
                    })
                    .then(resp => {
                        this.results = resp.data;
                        this.isLoading = false;
                    });
                this.autocompletesDisplays['works'][rowId] = true;
            },
            setResult(equipment, offerTabId, offerContentTabId, rowId) {
                console.log(this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]);
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['id'] = rowId;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['saveType'] = 'old';
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['base_id'] = equipment.id;
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
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['type'] = equipment.type.slug;
                // this.row.code = result;
                this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId][rowId] = false;
            },
            setWorkResult(work, index) {
                this.works[index] = {
                    name: work.name,
                    code: work.code,
                    points: work.points,
                    pivot:{quantity: 1},
                };
            },
            handleClickOutside(event) {
                if ('autocomplete-results' !== $(event.target).attr('class')) {
                    for (let i = 0; i < this.autocompletesDisplays['equipments'].length; i++) {
                        for (let j = 0; j < this.autocompletesDisplays['equipments'][i].length; j++) {
                            for (let k = 0; k < this.autocompletesDisplays['equipments'][i][j].length; k++) {
                                Vue.set(this.autocompletesDisplays['equipments'][i][j], k, false)
                            }
                        }
                    }
                    for (let i = 0; i < this.autocompletesDisplays['works'].length; i++) {
                        Vue.set(this.autocompletesDisplays['works'], i, false)
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
                                buffSelected[lastOfferTabId][lastOfferContentTabId] = buffRow[lastOfferTabId][lastOfferContentTabId]['rows'][0]['type_id'];
                                lastOfferContentTabId++;
                            });

                            lastOfferTabId++;
                        });
                        this.offerGroup.name = buffGroupName;
                        this.offersTabs = buffKP;
                        this.offersContentTabs = buffEq;
                        this.selected = buffSelected;
                        this.works = resp.data.equipment;
                        this.adjusters['number'] = resp.data.adjusters_number;
                        this.adjusters['days'] = resp.data.adjustments_days;
                        this.adjusters['fuel'] = resp.data.fuel_number;
                        this.adjusters['wage'] = resp.data.adjusters_wage;
                        this.adjusters['percentage'] = resp.data.pay_percentage;
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
                        this.selected[offerTabId].splice(i,1);
                        for(let j = i; j < this.offersContentTabs[offerTabId].length; j++){
                            this.offersContentTabs[offerTabId][j].id--;
                        }
                        break;
                    }
                }
            },
            deleteOffer(offerTabId){
                for(let i = 0; i < this.offersTabs.length; i++){
                    if(this.offersTabs[i].id === offerTabId){
                        this.offersTabs.splice(i, 1);
                        for(let j = i; j < this.offersTabs.length; j++){
                            this.offersTabs[j].id--;
                        }
                        this.offersContentTabs.splice(i, 1);
                        break;
                    }
                }
            },
            addWork(){
                this.works.push({
                    name: '',
                    code: '',
                    points: '',
                    pivot:{quantity: ''},
                });
                this.autocompletesDisplays['works'].push({[this.works.length-1]: false});
            },
            deleteWork(key){
                this.works.splice(key,1);
            }
        }
    }
</script>
