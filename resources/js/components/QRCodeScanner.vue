<template>
    <div>
        <h1 class="text-center">주문서 QR코드 스캔</h1>
        <qrcode-stream @decode="onDecode" :track="paintOutline" @init="onInit" v-if="!destroyed" class="qrcode">
            <div class="loading-indicator" v-if="loading">
                Loading...
            </div>
        </qrcode-stream>
        <b-card title="주문내역" v-if="data" class="mt-3 mb-3">
            <b-card-text>
                <p>주문번호: {{ data.order_id || data.order_number }}</p>
                <p>주문일: {{ data.order_date }}</p>
                <p>고객명: {{ data.buyer_name }}</p>
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
        <b-table striped bordered :items="orders" :fields="fields" head-variant="dark" show-empty>
            <template #empty="scope">
                <div class="text-center">주문 내역이 없습니다.</div>
            </template>
            <template #cell(order_id)="data">
                {{ data.item.order_id || data.item.order_number }}
            </template>
            <template #cell(state)="data">
                {{ data.item.delivery.state }}
            </template>
            <template #cell(reservation)="data">
                {{ data.item.delivery.reservation }}
            </template>
            <template #cell(action)="data">
                <b-button
                    variant="outline-primary"
                    size="sm"
                    @click="openOrderPickUp(data.item)"
                    :disabled="isReservationTomorrow(data.item.delivery)"
                >오더 픽업</b-button>
            </template>
        </b-table>
    </div>
</template>

<script>

export default {
    name: 'QRCodeScanner',

    props: ['branchOfficeId'],

    data() {
        return {
            data: null,
            loading: false,
            destroyed: false,
            orders: [],
            fields: [
                {
                    key: 'order_id',
                    label: '주문번호',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'state',
                    label: '배송상태',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'reservation',
                    label: '배송시간',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'buyer_name',
                    label: '주문자명',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'buyer_cellphone',
                    label: '연락처',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'receiver_address_full',
                    label: '배송주소',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                },
                {
                    key: 'action',
                    label: '',
                    sortable: false,
                    tdClass: 'align-middle',
                    thClass: 'text-center',
                }
            ]
        }
    },

    created() {
        this.getOrders();
    },

    methods: {
        async getOrders() {
            try{
                const response = await axios.get(`/api/branch/${this.branchOfficeId}/orders`);
                this.orders = response.data;
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
