<template>
    <div>
        <qrcode-stream @decode="onDecode" :track="paintOutline"></qrcode-stream>
        <b-card header="주문내역" class="mt-3">
            <b-card-text>
                <ul>
                    <li v-for="item in items" v-bind:key="item.id">
                        <span v-bind:class="{ pickup: item.isPickUp }">
                            {{ item.product_name }} (수량: {{ item.quantity }})
                        </span>
                    </li>
                </ul>
                <b-row>
                    <b-col class="text-center">
                        <b-button variant="primary" @click="completedPickUp">상품 픽업 완료</b-button>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
    </div>
</template>

<script>
export default {
    name: 'PickUp',

    props: ['details'],

    data() {
        return {
            items: this.details,
        }
    },

    methods: {
        onDecode(decodedString) {
            this.pickUpGoods(decodedString);
        },

        paintOutline (detectedCodes, ctx) {
            for (const detectedCode of detectedCodes) {
                const [ firstPoint, ...otherPoints ] = detectedCode.cornerPoints

                ctx.strokeStyle = "red";

                ctx.beginPath();
                ctx.moveTo(firstPoint.x, firstPoint.y);
                for (const { x, y } of otherPoints) {
                    ctx.lineTo(x, y);
                }
                ctx.lineTo(firstPoint.x, firstPoint.y);
                ctx.closePath();
                ctx.stroke();
            }
        },

        pickUpGoods(goodsCode) {
            this.items[0].isPickUp = true;
            this.items = [...this.items];
        },

        completedPickUp() {
            // TODO: update order pick up completed time
            location.href = '/my_orders'
        }
    },
}
</script>


<style scoped>
    .pickup {
        text-decoration: line-through;
    }
</style>

