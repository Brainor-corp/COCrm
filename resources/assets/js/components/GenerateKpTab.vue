<template>
    <div class="row">
        <div class="col-12 my-5">
            <div class="row">
                <div class="col-12 mb-3">
                    <form id="getOfferGroup" @submit.prevent="getOfferGroup">
                        <div class="row">
                            <div class="col-4">
                                <label for="getKPById">Вставить КП по ID</label>
                                <input type="text" id="getKPById" class="form-control" v-bind:disabled="redactMode" placeholder="id КП" v-model="groupId">
                            </div>
                            <div class="col-3 align-self-end">
                                <button class="btn btn-secondary" type="submit" v-bind:disabled="redactMode">Вставить КП</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12">
                    <form id="getOfferGroupByTemplate" @submit.prevent="getOfferGroup" style="width:100%">
                        <div class="row">
                        <div class="col-4">
                            <label for="getKPBySelect">Выбрать шаблон КП для вставки</label>
                            <select id="getKPBySelect" class="form-control" v-bind:disabled="redactMode" v-model="groupId">
                                <option disabled value="" selected>Выберите шаблон кп</option>
                                <option v-for="offerGroupTemplate in offerGroupTemplates" :value="offerGroupTemplate.id">{{ offerGroupTemplate.name }}</option>
                            </select>
                        </div>
                        <div class="col-3 align-self-end">
                            <button class="btn btn-secondary" type="submit" v-bind:disabled="redactMode">Вставить КП</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>


        </div>
        <div class="col-12 generate-kp-tab">
            <h3>Редактирование КП</h3>
            <form id="kp-generate-form" @change.prevent="updateOfferGroup">
                <input class="form-control my-3" type="text" :name="'offer_group[name]'" v-model="offerGroup.name"/>
                <h3>Оборудование</h3>
                <ul class="nav nav-tabs">
                    <li v-for="(offerTab, offerTabIndex) in offerGroup['offers']" :key="offerTabIndex" class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active show': offerTabIndex === 0 }" data-toggle="tab" :href="'#kp-edit-tab-'+offerTabIndex">
                            <div class="row align-items-baseline">
                                <div class="col-10">
                                    <input class="form-control offerTabContentName" type="text" :name="'offer_group[offers][offerTabIndex][name]'" v-model="offerTab.name"/><br>
                                </div>
                                <div class="col-2">
                                    <i @click="deleteOffer(offerTabIndex)" class="fas fa-times"></i>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item addOfferTab">
                        <a class="nav-link" @click.prevent="addOfferTab">+</a>
                    </li>
                </ul>

                <div class="type-tab tab-content mb-5">
                    <div v-for="(offerTab, offerTabIndex) in offerGroup['offers']" :key="offerTabIndex" :id="'kp-edit-tab-'+offerTabIndex" class="p-3 bg-light border tab-pane fade" v-bind:class="{ 'active show': offerTabIndex === 0 }">
                        <ul class="nav nav-tabs">
                            <li v-for="(offerContentTab, offerContentTabIndex) in offerTab['equipments']" :key="offerContentTabIndex" class="nav-item">
                                <a class="nav-link" v-bind:class="{ 'active show': offerContentTabIndex === 0 }" data-toggle="tab" :href="'#kp-'+offerTabIndex+'-content-edit-tab-'+offerContentTabIndex">
                                    <select v-bind:disabled="offerContentTabIndex === 'rashodnye-materialy'" class="type-select" :data-toggle="'tooltip-'+offerTabIndex+'-'+offerContentTabIndex" title="Сначала выберите тип">
                                        <option disabled value="">Выберите</option>
                                        <option v-for="(equipment, tabName) in offerTab['equipments']" v-bind:selected="tabName === offerContentTabIndex" :key="tabName" v-if="tabName" :value="tabName">{{ equipment.name }}</option>
                                    </select>
                                    <i class="fas fa-times text-body" v-if="offerContentTab.name === 'rashodnye-materialy'" @click="deleteTab(offerTabIndex, offerContentTabIndex)"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" @click.prevent="addOfferContentTab(offerTabIndex)">+</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div v-for="(offerContentTab, offerContentTabIndex) in offerTab['equipments']" :key="offerContentTabIndex" :id="'kp-'+offerTabIndex+'-content-edit-tab-'+offerContentTabIndex" class="bg-white border-left p-3 border-bottom tab-pane fade" v-bind:class="{ 'active show': offerContentTabIndex === 0 }">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">Артикул<span class="text-danger">*</span></th>
                                        <th scope="col">Название<span class="text-danger">*</span></th>
                                        <th scope="col">Описание</th>
                                        <th scope="col">Ед.измерения<span class="text-danger">*</span></th>
                                        <th scope="col">Количество<span class="text-danger">*</span></th>
                                        <th scope="col">Высчитанная цена<span class="text-danger">*</span></th>
                                        <th scope="col">Розн. цена<span class="text-danger">*</span></th>
                                        <th scope="col">3я колонка<span class="text-danger">*</span></th>
                                        <th scope="col">Спец. цена<span class="text-danger">*</span></th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(row, rowKey) in offerContentTab['equipment']" :key="rowKey">
                                        <td>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][base_id]'" v-model="rowKey"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][short_description]'" v-model="row.short_description"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_trade]'" v-model="row.price_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_special]'" v-model="row.price_special"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][comment]'" v-model="row.comment"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][type]'" v-model="row.type"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][type_id]'" v-model="row.type_id"/>
                                            <input class="form-control" type="text" @keyup="searchEquipmentByCode(row.code, offerTabIndex, offerContentTabIndex, rowKey)" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][code]'" v-model="row.code" />

                                            <ul :id="'autocomplete-results-e-'+offerTabIndex+'-'+offerContentTabIndex+'-'+rowKey" v-if="row['autoCompleteDisplay']" class="autocomplete-results">
                                                <li class="loading" v-if="isLoading">
                                                    Поиск...
                                                </li>
                                                <li v-else v-for="(result, i) in results" :key="i" @click.prevent="setResult(result, offerTabIndex, offerContentTabIndex, rowKey)" class="autocomplete-result">
                                                    {{ result['code'] + " - " + result['name'] }}
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][name]'" v-model="row.name"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][short_description]'" v-model="row.short_description"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][points]'" v-model="row.points"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][quantity]'" value="0"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price]'" v-model="row.price"/>
                                        </td>
                                        <td>
                                            <input @change="recalcPrice(offerTabIndex, offerContentTab.id, rowKey)" :id="'price-trade-'+offerTabIndex+'-'+offerContentTab.id+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_trade]'" v-model="row.price_trade"/>
                                        </td>
                                        <td>
                                            <input @change="recalcPrice(offerTabIndex, offerContentTab.id, rowKey)" :id="'price-small-trade-'+offerTabIndex+'-'+offerContentTab.id+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                        </td>
                                        <td>
                                            <input @change="recalcPrice(offerTabIndex, offerContentTab.id, rowKey)" :id="'price-special-'+offerTabIndex+'-'+offerContentTab.id+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTabIndex+']['+rowKey+'][price_special]'" v-model="row.price_special"/>
                                        </td>
                                        <td class="align-middle">
                                            <i @click="deleteRow(offerTabIndex, offerContentTab.id, rowKey)" class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!--<td colspan="10" v-bind:disabled="selected[offerTabIndex][offerContentTab.id] === 'new'" @click.prevent="addTableRow(offerTabIndex,offerContentTab.id)">+</td>-->
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
                        <label for="adjusters-no-tax">Цена (без налогов)</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-no-tax" v-model="adjusters['noTax']" :name="'offer_group[adjusters][adjusters_no_tax]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-number">Кол-во монтажников</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-number" v-model="adjusters['number']" :name="'offer_group[adjusters][adjusters_number]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-days">Дней работы</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-days" v-model="adjusters['days']" :name="'offer_group[adjusters][adjustment_days]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-fuel">Топливо</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-fuel" v-model="adjusters['fuel']" :name="'offer_group[adjusters][fuel]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-wage">Ставка</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="1" id="adjusters-wage" v-model="adjusters['wage']" :name="'offer_group[adjusters][adjusters_wage]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-percent">Процент монтажникам</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="1" max="100" id="adjusters-percent" v-model="adjusters['percentage']" :name="'offer_group[adjusters][pay_percentage]'"/>
                    </div>
                    <div class="align-self-end col-auto">
                        <button type="button" class="btn text-white btn-info" @click.prevent="calculatePrePrice()">Просчитать</button>
                    </div>
                    <div v-if="adjustmentPrePrice['VAT']" class="col-auto input-group-text m-3">
                        <span v-for="(value, key) in adjustmentPrePrice" :key="key" v-model="adjustmentPrePrice[key]"><strong>{{adjustmentPrePriceKeys[key]}}</strong>{{" " + value + "; &nbsp"}}</span>
                    </div>
                </div>
                <table class="table table-striped table-hover mt-2">
                    <thead>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название<span class="text-danger">*</span></th>
                        <th scope="col">Ед.измерения<span class="text-danger">*</span></th>
                        <th scope="col">Количество<span class="text-danger">*</span></th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(work, key) in works" :key="key">
                        <td>
                            <input class="form-control" type="hidden" hidden="hidden" :name="'offer_group[works]['+key+'][id]'" v-model="work.id"/>

                            <input class="form-control" type="text" @keyup="searchWorkByCode(work.code, key)" :name="'offer_group[works]['+key+'][code]'" v-model="work.code"/>
                            <!--<ul :id="'autocomplete-results-w'+key" v-show="autocompletesDisplays['works'][key]" class="autocomplete-results">-->
                                <!--<li class="loading" v-if="isLoading">-->
                                    <!--Поиск...-->
                                <!--</li>-->
                                <!--<li v-else v-for="(result, i) in results" :key="i" @click="setWorkResult(result, key)" class="autocomplete-result">-->
                                    <!--{{ result['code'] + " - " + result['name'] }}-->
                                <!--</li>-->
                            <!--</ul>-->
                        </td>
                        <td>
                            <input class="form-control" type="text" :name="'offer_group[works]['+key+'][name]'" v-model="work.name"/>
                        </td>
                        <td>
                            <input class="form-control" type="text" :name="'offer_group[works]['+key+'][points]'" v-model="work.points"/>
                        </td>
                        <td>
                            <input class="form-control" type="number" min="0" :name="'offer_group[works]['+key+'][quantity]'" v-model="work.pivot.quantity"/>
                        </td>
                        <td class="align-middle">
                            <i @click="deleteWork(key)" class="fas fa-times"></i>
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
    import NProgress from 'nprogress'

    export default {
        props: ['offerGroupID'],
        data(){
            return {
                offersVariantsCount : 1,
                offersTabs:[
                    { id: 0, name:'Вариант 1' },
                ],
                offersContentTabs:[[]],
                offerGroup:{name:'Шаблон КП'},
                works:[],
                adjusters:{
                    noTax: 1
                },
                adjustmentPrePrice:[],
                offerGroupTemplates:[],
                adjustmentPrePriceKeys:{
                    VAT: "НДС: ",
                    additionalDiscount: "Доп. Скидка: ",
                    adjustmentPrice: "Оплата монтажникам: ",
                    noTaxProfit: "Без налогов: ",
                    totalWorkPrice: "За работы с НДС: ",
                    totalWorkPriceNoVAT: "За работы без НДС: ",
                },
                results: [],
                groupId: '',
                autocompletesDisplays: {
                    equipments: [[[]]],
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
            axios.interceptors.request.use(config => {
                NProgress.start();
                return config;
            });
            axios.interceptors.response.use(response => {
                NProgress.done();
                return response;
            });
            axios.post('/getAllEquipmentTypes')
                .then((res) => {
                    this.types = res.data;

                    if(this.groupId !== null){
                         this.getOfferGroup();
                    }
                    else{
                        return axios.post('/getDefaultTabs');
                        // return axios.post('/getDefaultTypesWithEquipment')
                    }
                })
                .then((res) => {
                    if(res){
                        // console.log('--------default tab result-------');
                        // console.log(res.data);
                        // console.log('!-------default tab result------!');
                        let offer = res.data;
                        let offers = [];

                        $.each(offer, function (tabName, tab) {
                            $.each(tab['equipment'], function (equipmentKey, equipmentValue) {
                                equipmentValue['autoCompleteDisplay'] = false;
                            });

                        });

                        offers.push({
                            'name': 'Вариант 1',
                            'equipments': offer
                        });

                        this.offerGroup = {
                            'name': 'Шаблон КП',
                            'offers': offers,
                        };

                        console.log('--------offerGroup-------');
                        console.log(this.offerGroup);
                        console.log('!-------offerGroup------!');
                    }
                    // if(res) {
                    //     this.defaultTypes = res.data;
                    //     this.autocompletesDisplays['equipments'].push([]);
                    //     for (let i = 0; i < res.data.length; i++) {
                    //         this.offersContentTabs[0].push({
                    //             'id': i,
                    //             'name': res.data[i]['name'],
                    //             'rows': [],
                    //         });
                    //         this.selected[0].push(res.data[i]['id']);
                    //         this.autocompletesDisplays['equipments'][0].push([]);
                    //         if (res.data[i]['equipment'].length > 0) {
                    //             for (let j = 0; j < res.data[i]['equipment'].length; j++) {
                    //                 this.autocompletesDisplays['equipments'][0][i].push(false);
                    //                 this.offersContentTabs[0][i]['rows'].push({
                    //                     'id': j,
                    //                     'saveType': 'old',
                    //                     'base_id': res.data[i]['equipment'][j].id,
                    //                     'name': res.data[i]['equipment'][j].name,
                    //                     'quantity': res.data[i]['equipment'][j].pivot.quantity,
                    //                     'code': res.data[i]['equipment'][j].code,
                    //                     'type': this.types[res.data[i]['equipment'][j].type_id][0].slug,
                    //                     'type_id': this.types[res.data[i]['equipment'][j].pivot.type_id][0].id,
                    //                     'price': res.data[i]['equipment'][j].price,
                    //                     'price_trade': res.data[i]['equipment'][j].price_trade,
                    //                     'price_small_trade': res.data[i]['equipment'][j].price_small_trade,
                    //                     'price_special': res.data[i]['equipment'][j].price_special,
                    //                     'comment': res.data[i]['equipment'][j].pivot.comment,
                    //                     'short_description': res.data[i]['equipment'][j].short_description ? res.data[i]['equipment'][j].short_description : res.data[i]['equipment'][j].description,
                    //                     'points': res.data[i]['equipment'][j].points,
                    //                     'class': res.data[i]['equipment'][j].class,
                    //                 });
                    //             }
                    //         }
                    //     }
                        return axios.post('/getDefaultWorks')
                })
                .then((res) => {
                    if(res.data[0]) {
                        this.works = res.data[0].work;
                    }
                });
        },
        created: function (){
            this.updateOfferGroup;
            this.groupId = this.offerGroupID;
            axios.post('/getOfferGroupTemplates')
                .then(res=>{
                    this.offerGroupTemplates = res.data;
                });
        },
        mounted() {
            document.addEventListener("click", this.handleClickOutside);
        },
        destroyed() {
            document.removeEventListener("click", this.handleClickOutside);
        },
        methods: {
            pushDefaultTabs(){
                axios
                    .post('/getDefaultTabs')
                    .then((res) => {
                        if(res){
                            let offer = res.data;
                            let offers = this.offerGroup['offers'];

                            $.each(offer, function (tabName, tab) {
                                $.each(tab['equipment'], function (equipmentKey, equipmentValue) {
                                    equipmentValue['autoCompleteDisplay'] = false;
                                });

                            });

                            offers.push({
                                'name': 'Вариант ' + parseInt(this.offerGroup['offers'].length + 1),
                                'equipments': offer
                            });

                            this.offerGroup['offers'] = offers;
                        }
                        return axios.post('/getDefaultWorks');
                        //todo функции далее
                    })
            },
            updateOfferGroup() {
                this.$emit('updateOfferGroup');
                this.redactMode = true;
                NProgress.start();
                // $.each(this.offersContentTabs, (offerTabId, offerTab) => {
                //     $.each(offerTab, (offerContentTabId, offerContentTab) => {
                //         $.each(offerContentTab['rows'], (rowId, row) => {
                //             this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price'] = parseFloat(((parseFloat(row['price_small_trade']) - parseFloat(row['price_special']))/2) + parseFloat(row['price_special'])).toFixed(2);
                //         });
                //     });
                // });
                NProgress.done();

            },
            addOfferTab(){
                let lastOfferTab,lastOfferTabId;
                lastOfferTab = this.offersTabs[this.offersTabs.length - 1];
                lastOfferTabId = lastOfferTab ? lastOfferTab.id+1 : 0;
                this.offersVariantsCount++;
                this.offersTabs.push(
                    {

                        id: lastOfferTabId,
                        name:'Вариант '+this.offersVariantsCount
                    }
                );
                this.offersContentTabs.push([]);
                if(!this.selected[lastOfferTabId]){
                    this.selected[lastOfferTabId] = [];
                }
                this.pushDefaultTabs();
            },
            addOfferContentTab(offerTabId){
                let lastOfferContentTab;
                let lastOfferContentTabId;
                lastOfferContentTab =  this.offersContentTabs[offerTabId][this.offersContentTabs[offerTabId].length - 1];
                lastOfferContentTabId = lastOfferContentTab ? lastOfferContentTab.id+1 : 0;
                this.selected[offerTabId][lastOfferContentTabId] = 3;
                this.offersContentTabs[offerTabId].push(
                    {
                        id: lastOfferContentTabId,
                        name:'Новое оборудование',
                        rows:[]
                    }
                );
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

                    if(!this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId]){
                        this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId] = [];
                    }
                    this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId].push({lastRow: false});
                    this.offersContentTabs[offerTabId][offerContentTabId]['rows'].push(
                        {
                            id: lastRow,
                            saveType: 'new',
                            base_id: -1,
                            code: '',
                            name: '',
                            short_description: '',
                            quantity: '',
                            points: '',
                            price: '',
                            price_trade: '',
                            price_small_trade: '',
                            price_special: '',
                            comment: '',
                            class: this.types[this.selected[offerTabId][offerContentTabId]][0].class,
                            type: this.types[this.selected[offerTabId][offerContentTabId]][0].slug,
                            type_id: this.types[this.selected[offerTabId][offerContentTabId]][0].id,
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
                    .post('/findEquipmentByCode', {
                        code: codePart
                    })
                    .then(resp => {
                        this.results = resp.data;
                        this.isLoading = false;
                    });
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipment'][rowId]['autoCompleteDisplay'] = true;
            },
            searchWorkByCode(codePart, rowId){
                let context = this;
                axios.interceptors.request.use(function (config) {
                    context.isLoading = true;
                    return config;
                });

                axios
                    .post('/findWorkByCode', {
                        code: codePart
                    })
                    .then(resp => {
                        this.results = resp.data;
                        this.isLoading = false;
                    });
                this.autocompletesDisplays['works'][rowId] = true;

            },
            setResult(equipment, offerTabId, offerContentTabId, rowId) {
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['id'] = rowId;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['saveType'] = 'old';
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['base_id'] = equipment.id;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['code'] = equipment.code;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['name'] = equipment.name;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['short_description'] = equipment.short_description ? equipment.short_description : equipment.description;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['quantity'] = 1;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['points'] = equipment.points;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price'] = equipment.price;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_trade'] = equipment.price_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_small_trade'] = equipment.price_small_trade;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price_special'] = equipment.price_special;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['comment'] = equipment.comment;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['type_id'] = this.types[this.selected[offerTabId][offerContentTabId]][0].id;
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['type'] = this.types[this.selected[offerTabId][offerContentTabId]][0].slug;
                this.autocompletesDisplays['equipments'][offerTabId][offerContentTabId][rowId] = false;
            },
            setWorkResult(work, index) {
                this.works[index] = {
                    id: work.id,
                    name: work.name,
                    code: work.code,
                    points: work.points,
                    pivot:{quantity: 1},
                };
                this.autocompletesDisplays['works'][index] = false;
            },
            handleClickOutside(event) {
                if ($(event.target).attr('class') != 'autocomplete-results') {
                    $.each(this.offerGroup['offers'], (offerKey, offer) => {
                        $.each(offer['equipments'],  (tabKey, tab) => {
                            $.each(tab['equipment'], (rowKey, row) =>{
                                this.offerGroup['offers'][offerKey]['equipments'][tabKey]['equipment'][rowKey]['autoCompleteDisplay'] = false;
                            });
                        });
                    });
                }
            },
            getOfferGroup() {
                axios.interceptors.request.use(config => {
                    NProgress.start();
                    return config;
                });

                axios.interceptors.response.use(response => {
                    NProgress.done();
                    return response;
                });
                axios
                    .post('/getOfferGroup', {
                        id: this.groupId
                    })
                    .then(resp => {
                        this.offerGroup = resp.data
                    });
                // let lastOfferTabId = 0;
                // let buffKP = [];
                // let buffEq = [];
                // let buffRow = [];
                // let buffGroupName = '';
                // let buffSelected = [];
                // let buffAutocompletes = [];
                // axios
                //     .post('/getOfferGroup', {
                //         id: this.groupId
                //     })
                //     .then(resp => {
                //         buffGroupName = resp.data.name;
                //         resp.data.offers.forEach(function (offer) {
                //             let lastOfferContentTabId = 0;
                //             buffKP.push(
                //                 {
                //                     id: lastOfferTabId,
                //                     name: offer.name,
                //                 }
                //             );
                //             buffAutocompletes[lastOfferTabId] =[];
                //             Object.keys(offer['equipments']).forEach(function (type) {
                //                 let lastRow = 0;
                //                 offer['equipments'][type].forEach(function (equipment) {
                //                     buffAutocompletes[lastOfferTabId][lastOfferContentTabId] = false;
                //                     if(!buffRow[lastOfferTabId]){
                //                         buffRow[lastOfferTabId] = [];
                //                     }
                //                     if(!buffRow[lastOfferTabId][lastOfferContentTabId]){
                //                         buffRow[lastOfferTabId][lastOfferContentTabId] = [];
                //                     }
                //                     if(!buffRow[lastOfferTabId][lastOfferContentTabId]['rows']){
                //                         buffRow[lastOfferTabId][lastOfferContentTabId]['rows'] = [];
                //                     }
                //                     buffRow[lastOfferTabId][lastOfferContentTabId]['rows'].push(
                //                         {
                //                             id: lastRow,
                //                             saveType: 'old',
                //                             base_id: equipment.id,
                //                             code: equipment.code,
                //                             name: equipment.name,
                //                             short_description: equipment.short_description ? equipment.short_description : equipment.description,
                //                             quantity: equipment.pivot.quantity,
                //                             points: equipment.points,
                //                             price: equipment.price,
                //                             price_trade: equipment.price_trade,
                //                             price_small_trade: equipment.price_small_trade,
                //                             price_special: equipment.price_special,
                //                             comment: equipment.pivot.comment,
                //                             type_id: equipment.pivot.type_id,
                //                             type: equipment.pivot.type,
                //                             class: equipment.type.class,
                //                         }
                //                     );
                //                     lastRow++;
                //                 });
                //
                //                 if(!buffEq[lastOfferTabId]){
                //                     buffEq[lastOfferTabId] = [];
                //                 }
                //                 if(!buffRow[lastOfferTabId][lastOfferContentTabId]){
                //                     buffRow[lastOfferTabId][lastOfferContentTabId] = [];
                //                 }
                //                 if(!buffSelected[lastOfferTabId]){
                //                     buffSelected[lastOfferTabId] = [];
                //                 }
                //                 if(!buffSelected[lastOfferTabId][lastOfferContentTabId]){
                //                     buffSelected[lastOfferTabId][lastOfferContentTabId] = [];
                //                 }
                //                 buffEq[lastOfferTabId].push(
                //                     {
                //                         id: lastOfferContentTabId,
                //                         name: type,
                //                         rows: buffRow[lastOfferTabId][lastOfferContentTabId]['rows']
                //                     }
                //                 );
                //                 buffSelected[lastOfferTabId][lastOfferContentTabId] = buffRow[lastOfferTabId][lastOfferContentTabId]['rows'][0]['type_id'];
                //                 lastOfferContentTabId++;
                //             });
                //
                //             lastOfferTabId++;
                //         });
                //         this.offerGroup.name = buffGroupName;
                //         this.offersTabs = buffKP;
                //         this.offersContentTabs = buffEq;
                //         this.selected = buffSelected;
                //         this.works = resp.data.equipment;
                //         this.autocompletesDisplays['equipments'] = buffAutocompletes;
                //         this.adjusters['noTax'] = resp.data.adjusters_no_tax;
                //         this.adjusters['number'] = resp.data.adjusters_number;
                //         this.adjusters['days'] = resp.data.adjustments_days;
                //         this.adjusters['fuel'] = resp.data.fuel_number;
                //         this.adjusters['wage'] = resp.data.adjusters_wage;
                //         this.adjusters['percentage'] = resp.data.pay_percentage;
                //     });
            },
            deleteRow(offerTabId, offerContentTabId, rowId){
                for(let i = 0; i < this.offersContentTabs[offerTabId].length; i++){
                    if(this.offersContentTabs[offerTabId][i].id === offerContentTabId){
                        for(let j = 0; j < this.offersContentTabs[offerTabId][i]['rows'].length; j++) {
                            if(this.offersContentTabs[offerTabId][i]['rows'][j].id === rowId){
                                this.offersContentTabs[offerTabId][i]['rows'].splice(j, 1);
                                for(let k = j; k < this.offersContentTabs[offerTabId][i]['rows'].length; k++){
                                    this.offersContentTabs[offerTabId][i]['rows'][k].id--;
                                }
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
                this.offersVariantsCount--;
            },
            addWork(){
                this.works.push({
                    id: -1,
                    name: '',
                    code: '',
                    points: '',
                    pivot:{quantity: ''},
                });
                this.autocompletesDisplays['works'].push({[this.works.length-1]: false});
            },
            deleteWork(key){
                this.works.splice(key,1);
            },
            calculatePrePrice(){
                axios
                    .post('/calculatePrePrices',
                        deparam($('#kp-generate-form').serialize())
                    )
                    .then(res=>{
                        this.adjustmentPrePrice = res.data;
                    });
            },
            recalcAdjustments(){
                let adjustersNumber = parseFloat($('#adjusters-number').val());
                let adjustersDays = parseFloat($('#adjusters-days').val());
                let adjustersFuel = parseFloat($('#adjusters-fuel').val());
                let adjustersWage = parseFloat($('#adjusters-wage').val());
                let adjustersPercent = parseFloat($('#adjusters-percent').val());

                if(adjustersNumber + adjustersDays + adjustersFuel + adjustersWage + adjustersPercent){
                    let price = (adjustersNumber * adjustersDays * adjustersWage) + (adjustersDays * adjustersFuel);
                    this.adjusters['noTax'] = parseFloat(parseFloat((price * (100 - adjustersPercent)) / adjustersPercent) + parseFloat(price)).toFixed(2);
                    axios
                        .post('/calculatePrePrices',
                            deparam($('#kp-generate-form').serialize())
                        )
                        .then(res=>{
                            this.adjustmentPrePrice = res.data;
                        });
                }
            },
            recalcPrice(offerTabId, offerContentTabId, rowId){
                // let priceTrade = parseFloat($('#price-trade-'+offerTabId+'-'+offerContentTabId+'-'+rowId).val());
                let priceSmallTrade = parseFloat($('#price-small-trade-'+offerTabId+'-'+offerContentTabId+'-'+rowId).val());
                let priceSpecial = parseFloat($('#price-special-'+offerTabId+'-'+offerContentTabId+'-'+rowId).val());
                this.offersContentTabs[offerTabId][offerContentTabId]['rows'][rowId]['price'] = parseFloat((priceSmallTrade - priceSpecial) / 2 + priceSpecial).toFixed(2);
            },
        }
    }
</script>
