<template>
    <div class="row">
        {{offerGroup}}
        <div v-if="offerGroup.offer_group" class="col-12 kp-total-tab">
            <h2>{{ offerGroup.offer_group.name}}</h2>
            <div class="kp-total-offer" v-for="offerData in offerGroup.offer_group.offers">
                <h3>{{ offerData.name }}</h3>
                <table v-if="offerData.equipments && offerData.equipments" class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr><th colspan="8">Оборудование и расходные материалы</th></tr>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название</th>
                        <th scope="col">Описание</th>
                        <th scope="col">Количество</th>
                        <th scope="col">Цена</th>
                        <th scope="col">Розн. цена</th>
                        <th scope="col">Мин. розн. цена</th>
                        <th scope="col">Спец. цена</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="tab in offerData.equipments">
                        <tr v-if="row.type !== 'rashodnye-materialy'" v-for="row in tab">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.description }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.price }}</td>
                            <td>{{ row.price_trade }}</td>
                            <td>{{ row.price_small_trade }}</td>
                            <td>{{ row.price_special }}</td>
                        </tr>
                    </template>
                    <tr>
                        <td>----</td>
                        <td>Расходные материалы</td>
                        <td>Коробы, провода и т.д.</td>
                        <td>{{ (consumablePrice != 0) ? 1 : 0 }}</td>
                        <td>{{ consumablePrice }}</td>
                        <td> --- </td>
                        <td> --- </td>
                        <td> --- </td>
                    </tr>
                    <tr>
                        <td colspan="8" class="bg-light-blue text-right"><h4>Всего за оборудование {{ consumableSum }}р.</h4></td>
                    </tr>
                    </tbody>
                </table>
                <table v-if="offerGroup.offer_group.works" class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr><th colspan="4">Монтажные и пуско-наладочные работы</th></tr>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название</th>
                        <th scope="col">Ед.измерения</th>
                        <th scope="col">Количество</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in offerGroup.offer_group.works">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.points }}</td>
                            <td>{{ row.quantity }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="bg-beige text-right"><h4>Всего за работы ( с НДС ): р.</h4></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="bg-light-blue text-right"><h4>Всего за работы ( без НДС, Доп. скидка - ??? ): ???р.</h4></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12">
            <a href="" class="btn btn-success" v-if="offerGroup.offer_group" @click.prevent="saveOfferGroup">Сохранить группу КП</a>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['offerGroup', 'consumablePrice'],
        data(){
            return {

            };
        },
        computed: {
        },
        created: function (){},
        methods: {
            saveOfferGroup(){
                axios
                    .post(window.location.href + 'saveOfferGroup', this.offerGroup)
                    .then((res) => {
                        console.log(res);
                    });
            },
        }
    }
</script>
