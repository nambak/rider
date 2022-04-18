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
            id: null,
        }
    },

    methods: {
        async onDecode(decodedString) {
            if (this.isNotCompletedPickUp) {
                this.pickUpGoods(decodedString);
            }
        },

        pickUpGoods(goodsCode) {
            let index = 0;

            if (this.isBarcode(goodsCode)) {
                index = this.items.findIndex(item => item.barcode.indexOf(goodsCode) > -1 );
            } else {
                index = this.items.findIndex(item => item.product_code === goodsCode);
            }

            if (index !== -1) {
                const goods = this.items[index];

                if (goods.picked !== goods.quantity) {
                    goods.picked += 1;
                    this.$swal({
                        icon: 'success',
                        title: '확인',
                        text: goods.product_name + '(' + goods.picked + '/' + goods.quantity + ')' ,
                    })
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '오류',
                        text: '주문 수량 초과'
                    })
                }
            } else {
                return false;
            }

            this.checkCompletePickUp();
        },

        isBarcode(code) {
            let barcode = code.toString();
            return (barcode.charAt(0) === '8' && barcode.length === 13);
        },

        completedPickUp() {
            axios.post(`/api/order/${this.orderId}/packed`)
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

