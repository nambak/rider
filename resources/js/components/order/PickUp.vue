<template>
    <div>
        <h1 class="text-center">상품 코드 스캔</h1>
        <StreamBarcodeReader
            @decode="onDecode"
        ></StreamBarcodeReader>
        <b-card header="주문내역" class="mt-3">
            <b-card-text>
                <ul>
                    <li v-for="item in items" v-bind:key="item.id">
                        <span v-bind:class="{ pickup: item.picked === item.quantity }">
                            {{ item.product_name }} ({{ item.picked }}/{{ item.quantity }})
                        </span>
                    </li>
                </ul>
                <b-row>
                    <b-col class="text-center">
                        <b-button variant="primary" @click="completedPickUp">
                            상품 픽업 완료
                        </b-button>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
    </div>
</template>

<script>
import { StreamBarcodeReader } from 'vue-barcode-reader';

export default {
    name: 'PickUp',

    props: ['orderId'],

    components: { StreamBarcodeReader },

    mounted() {
        this.getOrderDetails();
    },

    data() {
        return {
            items: [],
            camera: 'auto',
            showScanConfirmation: false,
            isNotCompletedPickUp: true,
            loading: false,
            destroyed: false,
        }
    },

    methods: {
        async onDecode(decodedString) {
            if (this.isNotCompletedPickUp) {
                console.log(decodedString);
                this.pickUpGoods(decodedString);
            }
        },

        pickUpGoods(goodsCode) {
            let index = 0;

            if (this.isBarcode(goodsCode)) {
                index = this.items.findIndex(item => item.barcode === goodsCode);
            } else {
                index = this.items.findIndex(item => item.product_code === goodsCode);
            }


            if (index !== -1) {
                if (this.items[index].picked !== this.items[index].quantity) {
                    this.items[index].picked += 1;
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '오류',
                        text: '주문 수량 초과'
                    })
                }
            } else {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: '주문 내역에 없는 상품'
                });

                return false;
            }

            this.checkCompletePickUp();
        },

        isBarcode(code) {
            return (code.charAt(0) === 8 && code.length === 13);
        },

        completedPickUp() {
            // TODO: update order pick up completed time
            location.href = `/my_orders/${this.orderId}`
        },

        async getOrderDetails() {
            try {
                const response = await axios.get(`/api/order/${this.orderId}/details`);

                this.items = response.data.data;
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            }
        },

        checkCompletePickUp() {
            let filter = this.items.filter(item => item.quantity === item.picked);

            if (filter.length === this.items.length) {
                this.isNotCompletedPickUp = false;
            }
        }
    },
}
</script>

<style scoped>
    .pickup {
        text-decoration: line-through;
    }
</style>

