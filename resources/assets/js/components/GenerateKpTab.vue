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
                    <form id="getOfferGroupByTemplate" @submit.prevent="getOfferGroup" class="w-100">
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
                                    <input class="form-control offerTabContentName" type="text" :name="'offer_group[offers]['+offerTabIndex+'][name]'" v-model="offerTab.name"/><br>
                                </div>
                                <div class="col-2 align-self-baseline text-center">
                                    <i @click="deleteOffer(offerTabIndex)" class="fas fa-times"></i>
                                </div>
                                <div class="col-12">
                                    <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][description]'" v-model="offerTab.description"/><br>
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
                                    <div class="row">
                                    <!--<select v-bind:disabled="offerContentTab.slug === 'rashodnye-materialy'" class="type-select" :data-toggle="'tooltip-'+offerTabIndex+'-'+offerContentTabIndex" title="Сначала выберите тип" v-model="offerContentTab.slug">-->
                                        <!--<option disabled value="">Выберите</option>-->
                                        <!--<option v-for="(equipment, tabIndex) in offerTab['equipments']" v-bind:selected="equipment['slug'] === offerContentTab['slug']" :key="tabIndex" v-if="equipment['slug']" :value="equipment['slug']">{{ equipment.name }}</option>-->
                                    <!--</select>-->
                                        <div class="col-auto">
                                            <input @change="changeTabName(offerTabIndex, offerContentTabIndex)" type="text" class="form-control" v-bind:disabled="offerContentTab.slug === 'rashodnye-materialy'" v-model="offerContentTab.name">
                                        </div>
                                        <div class="col-auto align-self-center">
                                            <i class="fas fa-times text-body" v-if="offerContentTab.slug !== 'rashodnye-materialy'" @click="deleteTab(offerTabIndex, offerContentTabIndex)"></i>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="h-100 nav-link" @click.prevent="addOfferContentTab(offerTabIndex)">+</a>
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
                                    <tr v-for="(row, rowKey) in offerContentTab['equipments']" :key="rowKey">
                                        <td>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][name]'" v-model="offerContentTab.name"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][base_id]'" v-model="row.id"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][id]'" v-model="rowKey"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][short_description]'" v-model="row.short_description"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_trade]'" v-model="row.price_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_special]'" v-model="row.price_special"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][comment]'" v-model="row.comment"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][type]'" v-model="row.type"/>
                                            <input type="hidden" hidden="hidden" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][type_id]'" v-model="row.type_id"/>
                                            <input class="form-control" type="text" @keyup="searchEquipmentByCode(row.code, offerTabIndex, offerContentTabIndex, rowKey)" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][code]'" v-model="row.code" />

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
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][name]'" v-model="row.name"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][short_description]'" v-model="row.short_description"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][points]'" v-model="row.points"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][quantity]'" v-model="row.quantity"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price]'" v-model="row.price"/>
                                        </td>
                                        <td>
                                            <input @keyup="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" @change="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" :id="'price-trade-'+offerTabIndex+'-'+offerContentTabIndex+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_trade]'" v-model="row.price_trade"/>
                                        </td>
                                        <td>
                                            <input @keyup="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" @change="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" :id="'price-small-trade-'+offerTabIndex+'-'+offerContentTabIndex+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_small_trade]'" v-model="row.price_small_trade"/>
                                        </td>
                                        <td>
                                            <input @keyup="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" @change="recalcPrice(offerTabIndex, offerContentTabIndex, rowKey)" :id="'price-special-'+offerTabIndex+'-'+offerContentTabIndex+'-'+rowKey" class="form-control" type="number" min="0" :name="'offer_group[offers]['+offerTabIndex+'][equipments]['+offerContentTab.slug+'][equipment]['+rowKey+'][price_special]'" v-model="row.price_special"/>
                                        </td>
                                        <td class="align-middle">
                                            <i @click="deleteRow(offerTabIndex, offerContentTabIndex, rowKey)" class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" @click.prevent="addTableRow(offerTabIndex,offerContentTabIndex)">+</td>
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
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-no-tax" v-model="offerGroup['adjusters_no_tax']" :name="'offer_group[adjusters][adjusters_no_tax]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-number">Кол-во монтажников</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-number" v-model="offerGroup['adjusters_number']" :name="'offer_group[adjusters][adjusters_number]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-days">Дней работы</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-days" v-model="offerGroup['adjustments_days']" :name="'offer_group[adjusters][adjustment_days]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-fuel">Топливо</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="0" id="adjusters-fuel" v-model="offerGroup['fuel_number']" :name="'offer_group[adjusters][fuel]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-wage">Ставка</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="1" id="adjusters-wage" v-model="offerGroup['adjusters_wage']" :name="'offer_group[adjusters][adjusters_wage]'"/>
                    </div>
                    <div class="col-auto">
                        <label for="adjusters-percent">Процент монтажникам</label><br>
                        <input @change="recalcAdjustments" @keyup="recalcAdjustments" class="form-control" type="number" min="1" max="100" id="adjusters-percent" v-model="offerGroup['pay_percentage']" :name="'offer_group[adjusters][pay_percentage]'"/>
                    </div>
                    <div class="align-self-end col-auto">
                        <button type="button" class="btn text-white btn-info" @click.prevent="calculatePrePrice()">Просчитать</button>
                    </div>
                    <div v-if="adjustmentPrePrice['VAT']" class="col-auto input-group-text m-3">
                        <span v-for="(value, key) in adjustmentPrePrice" :key="key" v-model="adjustmentPrePrice[key]"><strong>{{adjustmentPrePriceKeys[key]}}</strong>{{" " + value + "; &nbsp"}}</span>
                    </div>
                </div>
                <ul class="nav nav-tabs mt-5" role="tablist">
                    <li v-for="(workTab, workTabIndex) in offerGroup['works']" :key="workTabIndex" class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active show': workTabIndex === 0 }" :id="'#kp-head-work-tab-'+workTabIndex" data-toggle="tab" role="tab" :href="'#kp-work-tab-'+workTabIndex">
                            <div class="row">
                                <div class="col-auto">
                                    <input @change="changeWorkTabName(workTabIndex)" type="text" class="form-control" v-model="workTab.name">
                                </div>
                                <div class="col-auto align-self-center">
                                    <i class="fas fa-times text-body" @click="deleteWorkTab(workTabIndex)"></i>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="h-100 nav-link" @click.prevent="addWorkTab()">+</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane" v-bind:class="{ 'active show': workTabIndex === 0 }" :aria-labelledby="'#kp-head-work-tab-'+workTabIndex" v-for="(workTab, workTabIndex) in offerGroup['works']" :key="workTabIndex" :id="'kp-work-tab-'+workTabIndex">
                        <table class="table table-striped table-hover bg-light border">
                            <thead>
                            <tr>
                                <th scope="col">Артикул</th>
                                <th scope="col">Название<span class="text-danger">*</span></th>
                                <th scope="col">Ед.измерения<span class="text-danger">*</span></th>
                                <th scope="col">Количество<span class="text-danger">*</span></th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody v-if="offerGroup['works']">
                            <input type="hidden" hidden="hidden" :name="'offer_group[works]['+workTab.slug+'][name]'" v-model="workTab.name"/>
                            <tr v-for="(work, key) in workTab['equipments']" :key="key">
                                <td>
                                    <input type="hidden" hidden="hidden" :name="'offer_group[works]['+workTab.slug+'][work]['+key+'][id]'" v-model="work.id"/>

                                    <input class="form-control" type="text" @keyup="searchWorkByCode(work.code, workTabIndex, key)" :name="'offer_group[works]['+workTab.slug+'][work]['+key+'][code]'" v-model="work.code"/>
                                    <ul :id="'autocomplete-results-w-'+key" v-show="work.autoCompleteDisplay === true" class="autocomplete-results">
                                        <li class="loading" v-if="isLoading">
                                            Поиск...
                                        </li>
                                        <li v-else v-for="(result, i) in results" :key="i" @click="setWorkResult(result, workTabIndex, key)" class="autocomplete-result">
                                            {{ result['code'] + " - " + result['name'] }}
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <input class="form-control" type="text" :name="'offer_group[works]['+workTab.slug+'][work]['+key+'][name]'" v-model="work.name"/>
                                </td>
                                <td>
                                    <input class="form-control" type="text" :name="'offer_group[works]['+workTab.slug+'][work]['+key+'][points]'" v-model="work.points"/>
                                </td>
                                <td>
                                    <input class="form-control" type="number" min="0" :name="'offer_group[works]['+workTab.slug+'][work]['+key+'][quantity]'" v-model="work.quantity"/>
                                </td>
                                <td class="align-middle">
                                    <i @click="deleteWork(workTabIndex, key)" class="fas fa-times"></i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9" @click.prevent="addWorkRow(workTabIndex)">+</td>
                            </tr>
                            </tbody>
                        </table>
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
    import NProgress from 'nprogress'

    export default {
        props: ['offerGroupID'],
        data(){
            return {
                offerGroup:{name:'Шаблон КП', adjusters_no_tax: 1},
                adjusters:{
                    noTax: 1
                },
                adjustmentPrePrice:[],
                offerGroupTemplates:[],
                adjustmentPrePriceKeys:{
                    VAT: 'НДС: ',
                    additionalDiscount: 'Доп. Скидка: ',
                    adjustmentPrice: 'Оплата монтажникам: ',
                    noTaxProfit: 'Без налогов: ',
                    totalWorkPrice: 'За работы с НДС: ',
                    totalWorkPriceNoVAT: 'За работы без НДС: ',
                },
                results: [],
                groupId: '',
                search: '',
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
                    }
                })
                .then((res) => {
                    if(res){
                        let offers = [];
                        $.each(res.data, function (tabName, tab) {
                            $.each(tab['equipments'], function (equipmentKey, equipmentValue) {
                                equipmentValue['autoCompleteDisplay'] = false;
                                equipmentValue['quantity'] = '0';
                            });

                        });

                        offers.push({
                            'name': 'Вариант 1',
                            'equipments': res.data
                        });

                        this.offerGroup = {
                            'name': 'Шаблон КП',
                            'offers': offers,
                        };

                    }
                    return axios.post('/getDefaultWorks')
                })
                .then((res) => {
                    if(res) {
                        let buf = this.offerGroup;
                        buf['works'] = res.data;
                        $.each(buf['works'], (workTabKey, workTab) =>{
                            $.each(workTab['equipments'], (key, value) =>{
                                value['autoCompleteDisplay'] = false;
                                value['quantity'] = 0;
                            });
                        });

                        this.offerGroup = {
                            'name': 'Шаблон КП',
                            'offers': buf.offers,
                            'works': buf.works,
                            'adjusters_no_tax': 1
                        };
                        this.$forceUpdate();
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
                                $.each(tab['equipments'], function (equipmentKey, equipmentValue) {
                                    equipmentValue['autoCompleteDisplay'] = false;
                                    equipmentValue['quantity'] = '0';
                                });
                            });

                            offers.push({
                                'name': 'Вариант ' + parseInt(this.offerGroup['offers'].length + 1),
                                'equipments': offer
                            });

                            this.offerGroup['offers'] = offers;
                        }
                        return axios.post('/getDefaultWorks');
                    })
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
                        $.each(resp.data['offers'], function (offerKey, offer) {
                            $.each(offer['equipments'], function (tabKey, tabValue){
                                $.each(tabValue['equipments'], function (equipmentKey, equipmentValue) {
                                    equipmentValue['autoCompleteDisplay'] = false;
                                    equipmentValue['price'] = equipmentValue['pivot']['price'];
                                    equipmentValue['price_trade'] = equipmentValue['pivot']['price_trade'];
                                    equipmentValue['price_small_trade'] = equipmentValue['pivot']['price_small_trade'];
                                    equipmentValue['price_special'] = equipmentValue['pivot']['price_special'];
                                    equipmentValue['quantity'] = equipmentValue['pivot']['quantity'] ? equipmentValue['pivot']['quantity'] : 0;
                                });
                            });
                        });
                        $.each(resp.data.works, function (workTabKey, workTab) {
                            $.each(workTab.equipments, function (workKey, work) {
                                work['autoCompleteDisplay'] = false;
                                work['quantity'] = work['pivot']['quantity'] ? work['pivot']['quantity'] : 0;
                            });
                        });
                        this.offerGroup = resp.data;
                        this.$forceUpdate();
                    });
            },
            updateOfferGroup() {
                this.$emit('updateOfferGroup');
                this.redactMode = true;
                NProgress.start();
                NProgress.done();

            },
            addOfferTab(){
                this.pushDefaultTabs();
            },
            addOfferContentTab(offerTabId){
                this.offerGroup['offers'][offerTabId]['equipments'].push({
                    'equipments': [],
                    'name': "Новая вкладка",
                    'slug': "equipment-tab-" + this.offerGroup['offers'][offerTabId]['equipments'].length,
                });
            },
            addTableRow(offerTabId, offerContentTabId){
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'].push(
                    {
                        id: -1,
                        autoCompleteDisplay: false,
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
                    }
                );
            },
            changeTabName(offerTabId, offerContentTabId){
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId].slug = "equipment-tab-" + offerContentTabId;
            },
            deleteRow(offerTabId, offerContentTabId, rowId){
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'].splice(rowId, 1);
            },
            deleteTab(offerTabId, offerContentTabId){
                this.offerGroup['offers'][offerTabId]['equipments'].splice(offerContentTabId, 1);
            },
            deleteOffer(offerTabId){
                this.offerGroup['offers'].splice(offerTabId, 1);
            },
            addWorkTab(){
                this.offerGroup.works.push({
                    'name': 'Вкладка для работ',
                    'slug': "work-tab-" + this.offerGroup['works'].length,
                    'equipments': [],
                });
            },
            addWorkRow(workTabIndex){
                this.offerGroup.works[workTabIndex]['equipments'].push({
                    id: -1,
                    name: '',
                    code: '',
                    points: '',
                    quantity: 0,
                    autoCompleteDisplay: false,
                });
            },
            deleteWorkTab(workTabIndex){
                this.offerGroup.works.splice(workTabIndex, 1);
            },
            deleteWork(workTabIndex, key){
                this.offerGroup.works[workTabIndex]['equipments'].splice(key,1);
            },
            changeWorkTabName(workTabIndex){
                this.offerGroup['works'][workTabIndex].slug = "work-tab-" + workTabIndex;
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
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'][rowId]['autoCompleteDisplay'] = true;
            },
            searchWorkByCode(codePart, workTabId, rowId){
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
                this.offerGroup.works[workTabId]['equipments'][rowId]['autoCompleteDisplay'] = true;

            },
            setResult(equipment, offerTabId, offerContentTabId, rowId) {
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'][rowId] = equipment;
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'][rowId]['autoCompleteDisplay'] = false;
                this.$forceUpdate();
            },
            setWorkResult(work, workTabIndex, index) {
                this.offerGroup.works[workTabIndex]['equipments'][index] = work;
                this.offerGroup.works[workTabIndex]['equipments'][index]['autoCompleteDisplay'] = false;
                this.offerGroup.works[workTabIndex]['equipments'][index]['quantity'] = 0;
                this.$forceUpdate();
            },
            handleClickOutside(event) {
                if ($(event.target).attr('class') != 'autocomplete-results') {
                    $.each(this.offerGroup['offers'], (offerKey, offer) => {
                        $.each(offer['equipments'],  (tabKey, tab) => {
                            $.each(tab['equipments'], (rowKey, row) =>{
                                this.offerGroup['offers'][offerKey]['equipments'][tabKey]['equipments'][rowKey]['autoCompleteDisplay'] = false;
                            });
                        });
                    });
                    $.each(this.offerGroup['works'], (workTabKey, workTab) => {
                        $.each(workTab['equipments'], (workKey, work) => {
                            this.offerGroup['works'][workTabKey]['equipments'][workKey]['autoCompleteDisplay'] = false;
                        });
                    });
                }
                this.$forceUpdate();
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
                    this.offerGroup['adjusters_no_tax'] = parseFloat(parseFloat((price * (100 - adjustersPercent)) / adjustersPercent) + parseFloat(price)).toFixed(2);
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
                this.offerGroup['offers'][offerTabId]['equipments'][offerContentTabId]['equipments'][rowId]['price'] = parseFloat((priceSmallTrade - priceSpecial) / 2 + priceSpecial).toFixed(2);
            },
        }
    }
</script>
