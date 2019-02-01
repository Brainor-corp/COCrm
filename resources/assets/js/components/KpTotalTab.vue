<template>
    <div class="row">
        <div class="col-12 kp-total-tab">
            {{offerGroup}}
            <h2>{{ offerGroup.offer_group.name}}</h2>
            <div class="kp-total-offer" v-for="offerData in offerGroup.offer_group.offers">
                <h3>{{ offerData.name }}</h3>
                <!--<div class="kp-total-offer-content" v-for="offerContent in offerData.equipments">-->
                    <!--<h4>{{ offerContent.name }}</h4>-->
                        <!--<h4 class="text-capitalize">{{offerContent.type_name}}</h4>-->
                    <table v-if="offerData.equipments.type_name === 'работа'" class="table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">Артикул</th>
                            <th scope="col">Название</th>
                            <th scope="col">Ед.измерения</th>
                            <th scope="col">Количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="row.quantity && row.quantity!='0'" v-for="row in offerData.equipments">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.points }}</td>
                            <td>{{ row.quantity }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table v-else class="table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">Артикул</th>
                            <th scope="col">Название</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Количество</th>
                            <th scope="col">Цена</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="row.quantity && row.quantity!='0'" v-for="row in offerData.equipments">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.description }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.price }}</td>
                        </tr>
                        </tbody>
                    </table>
                <!--</div>-->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['offerGroup'],
        data(){
            return {
            };
        },
        computed: {
        },
        created: function (){},
        methods: {}
    }
</script>
