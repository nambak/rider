<template>
    <div>
        <qrcode-stream @decode="onDecode" :track="paintOutline" @init="onInit"></qrcode-stream>
        <b-card title="주문내역" v-if="data" class="mt-3">
            <b-card-text>
                <p>주문번호: {{ data.order_id }}</p>
                <p>주문일: {{ data.order_date }}</p>
                <p>고객연락처: {{ data.buyer_cellphone }}</p>
                <p>주소: {{ data.receiver_address_full }}</p>
                <p>메세지: {{ data.shipping_message }}</p>
            </b-card-text>
            <b-row>
                <b-col class="text-center">
                    <b-button variant="primary" @click="getOrderPickUp">오더 픽업 하기</b-button>
                </b-col>
            </b-row>
        </b-card>
    </div>
</template>

<script>
import {QrcodeCapture, QrcodeDropZone, QrcodeStream} from 'vue-qrcode-reader'

export default {
    name: 'QRCodeScanner',

    components: {
        QrcodeStream,
        QrcodeDropZone,
        QrcodeCapture
    },

    data() {
        return {
            data: null,
        }
    },

    methods: {
        onDecode(decodedString) {
            this.getOrderDetails(decodedString);
        },

        onInit(promise) {
            promise.catch(error => {
                console.error(error.message);
            });
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

        async getOrderDetails(orderNumber)
        {
            try {
                const response = await axios.get(`/api/order/${orderNumber}`);

                if (response.status === 200) {
                    this.data = response.data;
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    message: e.message
                });
            }
        },

        getOrderPickUp()
        {
            location.href=`/order/${this.data.id}/pick_up`;
        }
    }
}
</script>
