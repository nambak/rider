<template>
    <div>
        <v-app-bar color="blue" dark app top absolte>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer">
            </v-app-bar-nav-icon>
            <v-toolbar-title>주문상세</v-toolbar-title>
            <v-spacer></v-spacer>
        </v-app-bar>
        <v-navigation-drawer v-model="drawer" absolute temporary>
            <v-list nav dense>
                <v-list-item-group v-model="group" active-class="deep-purple--text text--accent-4">
                    <v-list-item>
                        <v-list-item-title>{{ userName }}</v-list-item-title>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title>
                            <a class="dropdown-item" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="/logout" method="POST" class="d-none">
                                @csrf
                            </form>
                        </v-list-item-title>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>
        <v-main>
            <v-container>
                <v-card>
                    <v-card-title>배송정보</v-card-title>
                    <v-card-text>
                        <ul>
                            <li>주문자명</li>
                            <li>주문번호</li>
                            <li>배송주소</li>
                            <li>연락처</li>
                            <li>배송메세지</li>
                        </ul>
                    </v-card-text>
                </v-card>
                <v-card class="mt-3">
                    <v-card-title>주문내역</v-card-title>
                    <v-card-text>
                        <ul>
                            <li v-for="item in items" v-bind:key="item.id">
                                <span>{{ item.product_name }}</span>
                                <span class="font-weight-bold">{{ item.quantity }}개</span>
                            </li>
                        </ul>
                    </v-card-text>
                </v-card>
                <v-btn
                    depressed
                    color="primary"
                    block
                    x-large
                    @click="completedPickUp"
                    class="mt-3"
                >배송시작</v-btn>
            </v-container>
        </v-main>
        <v-bottom-navigation background-color="indigo" dark app bottom>
            <v-btn>
                <span>전체 주문</span>
                <v-icon>mdi-reorder-horizontal</v-icon>
            </v-btn>
            <v-btn>
                <span>내 주문</span>
                <v-icon>mdi-inbox</v-icon>
            </v-btn>
            <v-btn>
                <span>배송 내역</span>
                <v-icon>mdi-history</v-icon>
            </v-btn>
        </v-bottom-navigation>
    </div>
</template>

<script>
import { StreamBarcodeReader } from 'vue-barcode-reader';

export default {
    name: 'PickUp',

    props: ['orderId', 'userName'],

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
            drawer: false,
            group: null,
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

