<template>
    <div class="row">
        <div class="col-12 kp-total-tab">
            <div class="kp-total-offer" v-for="offerData in offerGroup.offer_group">
                <h3>{{ offerData.name }}</h3>
                <div class="kp-total-offer-content" v-for="offerContent in offerData.offers">
                    <h4>{{ offerContent.name }}</h4>
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
                        <tr v-for="row in offerContent.rows">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.points }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.price }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
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
