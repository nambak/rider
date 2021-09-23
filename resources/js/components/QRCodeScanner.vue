<template>
    <div>
        <h1 class="text-center">주문서 QR코드 스캔</h1>
        <qrcode-stream @decode="onDecode" :track="paintOutline" @init="onInit"></qrcode-stream>
        <p class="error">{{ error }}</p>
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

export default {
    name: 'QRCodeScanner',

    data() {
        return {
            data: null,
            error: '',
        }
    },

    methods: {
        onDecode(decodedString) {
            this.getOrderDetails(decodedString);
        },

        async onInit (promise) {
            try {
                await promise
            } catch (error) {
                if (error.name === 'NotAllowedError') {
                    this.error = "ERROR: you need to grant camera access permission"
                } else if (error.name === 'NotFoundError') {
                    this.error = "ERROR: no camera on this device"
                } else if (error.name === 'NotSupportedError') {
                    this.error = "ERROR: secure context required (HTTPS, localhost)"
                } else if (error.name === 'NotReadableError') {
                    this.error = "ERROR: is the camera already in use?"
                } else if (error.name === 'OverconstrainedError') {
                    this.error = "ERROR: installed cameras are not suitable"
                } else if (error.name === 'StreamApiNotSupportedError') {
                    this.error = "ERROR: Stream API is not supported in this browser"
                } else if (error.name === 'InsecureContextError') {
                    this.error = 'ERROR: Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.';
                } else {
                    this.error = `ERROR: Camera error (${error.name})`;
                }
            }
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
