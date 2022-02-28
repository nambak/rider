<template>
    <div>
        <v-app-bar color="blue" dark app top absolte>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer">
            </v-app-bar-nav-icon>
            <v-toolbar-title>전체주문</v-toolbar-title>
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
            <v-list three-line v-if="items.length > 0">
                <template v-for="(item, index) in items">
                    <v-list-item :key="item.id" @click="openOrderPickUp(item)">
                        <v-list-item-content>
                            <v-list-item-title v-html="item.buyer_name"></v-list-item-title>
                            <v-list-item-subtitle v-html="item.receiver_address_full"></v-list-item-subtitle>
                            <v-list-item-subtitle v-html="item.buyer_cellphone"></v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="mr-3">{{ item.region | regionForHuman }}</v-list-item-action-text>
                            <div v-if="item.delivery.reservation !== '즉시배송'">
                                <v-chip color="yellow" small class="mt-1">예약배송</v-chip><br />
                                <span class="ml-2 caption">20~21시</span>
                            </div>
                            <div v-else>
                                <v-chip small class="mt-1" color="blue" dark>즉시배송</v-chip>
                            </div>
                        </v-list-item-action>
                    </v-list-item>
                    <v-divider
                        v-if="index < items.length - 1"
                        :key="index"
                    ></v-divider>
                </template>
            </v-list>
            <v-container v-else>
                <v-row style="height: 150px;">
                    <v-col align-self="center">
                        <div class="text-center">주문이 없습니다.</div>
                    </v-col>
                </v-row>
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

export default {
    name: 'QRCodeScanner',

    props: ['branchOfficeId', 'userName'],

    data() {
        return {
            drawer: false,
            group: null,
            data: null,
            selected: [2],
            items: [],
        }
    },

    watch: {
        group () {
            this.drawer = false
        },
    },

    created() {
        this.getOrders();
    },

    methods: {
        async getOrders() {
            try{
                const response = await axios.get(`/api/branch/${this.branchOfficeId}/orders`);
                this.items = response.data;
            } catch (error) {
                this.$swal({
                    icon: 'error',
                    text: error.response.data.message || error.message,
                });
            }
        },

        async onInit(promise) {
            this.loading = true;

            try {
                await promise
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        onDecode(decodedString) {
            this.getOrderDetails(decodedString);
        },

        paintOutline(detectedCodes, ctx) {
            for (const detectedCode of detectedCodes) {
                const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

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

        async getOrderDetails(orderNumber) {
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

        getOrderPickUp() {
            axios.post(`/api/order/${this.data.id}/pickup`);
            location.href = `/order/${this.data.id}/pick_up`;
        },

        openOrderPickUp(order) {
            if (order.delivery.order_packed_at === null) {
                window.open(`/order/${order.id}/pick_up`, '_blank');
            } else if (order.delivery.started_at === null) {
                window.open(`/my_orders/${order.id}`, '_blank');
            } else if (order.delivery.completed_at === null) {
                window.open(`/my_orders/${order.id}`, '_blank');
            }
        },

        isReservationTomorrow(delivery) {
            return (delivery.reservation.includes('내일'));
        }
    },

    filters: {
        shortDetails(details) {
            if (details.length === 1) {
                return details[0].product_name;
            } else {
                return `${details[0].product_name} 외 ${details.length - 1} 건`;
            }
        },

        regionForHuman(region) {
            if (region === 'path' || region === 'blue') {
                return '10~15분';
            } else if (region === 'orange') {
                return '15~25분';
            }
        }
    }
}
</script>

<style scoped>
    .loading-indicator {
        font-weight: bold;
        font-size: 2rem;
        text-align: center;
    }

    .qrcode {
        height: 250px;
    }
</style>
