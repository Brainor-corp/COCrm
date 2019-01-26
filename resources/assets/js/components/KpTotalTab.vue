<template>
    <div class="row">
        <div class="col-12 kp-total-tab">
            <h3>Панель 2</h3>
            <p>Содержимое 2 панели...</p>
            <ul>
                <li v-for="offer in offerGroup">
                    {{ offer.message }}
                </li>
            </ul>
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
