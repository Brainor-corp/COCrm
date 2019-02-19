<template>
    <div class="row">
        {{offerGroup}}
        <div v-if="offerGroup.offer_group" class="col-12 kp-total-tab">
            <h2>{{ offerGroup.offer_group.name}}</h2>
            <div class="kp-total-offer" v-for="(offerData, i) in offerGroup.offer_group.offers" :key="i">
                <h3>{{ offerData.name }}</h3>
                <div class="my-4 h4 font-weight-bold">Оборудование и расходные материалы</div>
                <table v-if="offerData.equipments && offerData.equipments" class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Наименование</th>
                        <th scope="col">Описание</th>
                        <th scope="col">Количество</th>
                        <th scope="col">Цена</th>
                        <th scope="col">Высчитанная цена</th>
                        <!--<th scope="col">Розн. цена</th>-->
                        <!--<th scope="col">Мин. розн. цена</th>-->
                        <!--<th scope="col">Спец. цена</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(tab, type) in offerData.equipments" v-if="type !== 'rashodnye-materialy'">
                        <tr v-for="row in tab">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.description }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.price }}</td>
                            <td>{{ row.counted_price }}</td>
                            <!--<td>{{ row.price_trade }}</td>-->
                            <!--<td>{{ row.price_small_trade }}</td>-->
                            <!--<td>{{ row.price_special }}</td>-->
                        </tr>
                    </template>
                    <tr v-if="calcPrices[i]">
                        <td>----</td>
                        <td>Расходные материалы</td>
                        <td>Коробы, провода и т.д.</td>
                        <td>{{ (calcPrices[i]['consumablePrice'] != 0) ? 1 : 0 }}</td>
                        <td>{{ calcPrices[i]['consumablePrice'] }}</td>
                        <td> --- </td>
                    </tr>
                    <tr v-if="calcPrices[i]">
                        <!--<td colspan="8" class="bg-light-blue text-right"><h4>Всего за оборудование {{ calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] }}р.</h4></td>-->
                    </tr>
                    </tbody>
                </table>
                <div v-if="calcPrices[i]" class="bg-light-blue text-right my-5">
                    <h4 class="p-3  font-weight-bold">Всего за оборудование {{ calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] }}р.</h4>
                </div>
                <div v-if="offerGroup.offer_group.works" class="my-4 h4 font-weight-bold">Монтажные и пуско-наладочные работы</div>
                <table v-if="offerGroup.offer_group.works" class="table table-bordered">
                    <thead>
                    <!--<tr><th colspan="4">Монтажные и пуско-наладочные работы</th></tr>-->
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название</th>
                        <th scope="col">Ед.измерения</th>
                        <th scope="col">Количество</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-if="offerGroup.offer_group.works.length > 0" v-for="row in offerGroup.offer_group.works">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.points }}</td>
                            <td>{{ row.quantity }}</td>
                        </tr>
                        <tr v-else>
                            <td> ---- </td>
                            <td> ---- </td>
                            <td> ---- </td>
                            <td> ---- </td>
                        </tr>
                        <!--<tr v-if="calcPrices[i]">-->
                            <!--<td colspan="4" class="bg-beige text-right"><h4>Всего за работы ( с НДС ): {{ calcPrices[i]['totalWorkPrice'] }}р.</h4></td>-->
                        <!--</tr>-->
                        <!--<tr v-if="calcPrices[i]">-->
                            <!--<td colspan="4" class="bg-light-blue text-right"><h4>Всего за работы ( без НДС, Доп. скидка - {{ calcPrices[i]['additionalDiscount'] }} ): {{calcPrices[i]['totalWorkPriceNoVAT']}}р.</h4></td>-->
                        <!--</tr>-->
                        <!--<tr v-if="calcPrices[i]">-->
                            <!--<td colspan="4" class="bg-beige text-right"><h4>Общая стоимость (1 договор - все с НДС): {{ calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] + calcPrices[i]['totalWorkPrice'] }}р.</h4></td>-->
                        <!--</tr>-->
                        <!--<tr v-if="calcPrices[i]">-->
                            <!--<td colspan="4" class="bg-light-blue text-right"><h4>Общая стоимость (2 договора - оборудование с НДС, работы без НДС): {{calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] + calcPrices[i]['totalWorkPriceNoVAT']}}р.</h4></td>-->
                        <!--</tr>-->
                    </tbody>
                </table>
                <div v-if="calcPrices[i]" class="my-5">
                    <div class="bg-beige text-right"><h4 class="p-3  font-weight-bold mb-0">Всего за работы ( с НДС ): {{ calcPrices[i]['totalWorkPrice'] }}р.</h4></div>
                    <div class="bg-light-blue text-right"><h4 class="p-3  font-weight-bold mb-0">Всего за работы ( без НДС, <span class="text-danger">Доп. скидка - {{ calcPrices[i]['additionalDiscount'] }} </span>): {{calcPrices[i]['totalWorkPriceNoVAT']}}р.</h4></div>
                </div>
                <div v-if="calcPrices[i]" class="my-5">
                    <div class="bg-beige text-right"><h4 class="p-3  font-weight-bold mb-0">Общая стоимость (1 договор - все с НДС): {{ calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] + calcPrices[i]['totalWorkPrice'] }}р.</h4></div>
                    <div class="bg-light-blue text-right"><h4 class="p-3  font-weight-bold mb-0">Общая стоимость (2 договора - оборудование с НДС, работы без НДС): {{calcPrices[i]['equipmentPrice'] + calcPrices[i]['consumablePrice'] + calcPrices[i]['totalWorkPriceNoVAT']}}р.</h4></div>
                </div>
            </div>
        </div>
        <div class="mx-auto mb-5">
            <a href="" class="btn btn-lg btn-primary create-btn d-flex align-items-center justify-content-center" v-if="offerGroup.offer_group" @click.prevent="saveOfferGroup">Сохранить группу КП</a>
            <a :href="url" target="_blank" v-if="url">{{url}}</a>
            <span v-if="err" class="text-danger">{{err}}</span>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['offerGroup', 'calcPrices', 'offerGroupID'],
        data(){
            return {
                url: "",
                err: "",
            };
        },
        computed: {
        },
        created: function (){},
        methods: {
            saveOfferGroup(){
                if(this.offerGroupID == null){
                    axios
                        .post('/saveOfferGroup', this.offerGroup)
                        .then((res) => {
                            this.url = res.data;
                        })
                        .catch(error => this.err = error);
                }
                else{
                    axios
                        .post('/updateOfferGroup', [this.offerGroup, this.offerGroupID])
                        .then((res) => {
                            console.log(res.data);
                            // this.url = res.data;
                        })
                        .catch(error => this.err = error);
                }
            },
        }
    }
</script>
