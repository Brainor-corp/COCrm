<template>
    <div class="row">
        {{offerGroup}}
        <div v-if="offerGroup.offer_group" class="col-12 kp-total-tab">
            <h2>{{ offerGroup.offer_group.name}}</h2>
            <div class="kp-total-offer" v-for="offerData in offerGroup.offer_group.offers">
                <h3>{{ offerData.name }}</h3>
                <table v-if="offerData.equipments && offerData.equipments.equipment" class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr><th colspan="5">Оборудование и расходные материалы</th></tr>
                    <tr>
                        <th scope="col">Артикул</th>
                        <th scope="col">Название</th>
                        <th scope="col">Описание</th>
                        <th scope="col">Количество</th>
                        <th scope="col">Цена</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="tab in offerData.equipments.equipment">
                        <tr v-if="row.type !== 'rashodnye-materialy'" v-for="row in tab">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.description }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.price }}</td>
                        </tr>
                    </template>
                    <tr>
                        <td>----</td>
                        <td>Расходные материалы</td>
                        <td>Коробы, провода и т.д.</td>
                        <td>{{ (consumablePrice != 0) ? 1 : 0 }}</td>
                        <td>{{ consumablePrice }}</td>
                    </tr>
                    </tbody>
                </table>
                <table v-if="offerData.equipments && offerData.equipments.work" class="table table-striped table-hover table-bordered">
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
                    <template v-for="tab in offerData.equipments.work">
                        <tr v-for="row in tab">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.points }}</td>
                            <td>{{ row.quantity }}</td>
                        </tr>
                    </template>
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
