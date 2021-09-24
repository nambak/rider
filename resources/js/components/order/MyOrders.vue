<template>
    <div>
        <b-card :header="state" :header-bg-variant="stateColor" header-text-variant="white">
            <b-card-text>
                <p>주문번호: {{ data.order_id }}</p>
                <p>주문일: {{ data.order_date }}</p>
                <p>고객연락처: {{ data.buyer_cellphone }}</p>
                <p>주소: {{ data.receiver_address_full }}</p>
                <p>메세지: {{ data.shipping_message }}</p>

                <b-row>
                    <b-col class="text-center">
                        <b-button variant="primary" @click="startDelivery">{{ actionName }}</b-button>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
    </div>
</template>

<script>
export default {
    name: 'MyOrders',

    props: ['order'],

    data() {
        return {
            data: [],
            state: '픽업완료',
        }
    },

    async mounted() {
        // TODO: 담당자별 주문만 가자올것.

        try {
            const response = await axios.get(`/api/order/${this.order.order_id}`);

            if (response.status === 200) {
                this.data = response.data;
            }
        } catch (e) {
            this.$swal({
                icon: 'error',
                title: '오류',
                text: e.message
            });
        }
    },

    methods: {
        startDelivery() {
            if (this.state === '픽업완료') {
                this.createCafe24Shipment();
            } else if (this.state === '배송중') {
                location.href = `/order/${this.order.id}/delivery_complete`;
            }
        },

        async createCafe24Shipment() {
            try {
                const response = await axios.post(`/api/order/${this.data.id}/shipment`);

                if (response.status === 200) {
                    this.state = '배송중';
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            }
        }
    },

    computed: {
        stateColor() {
            switch (this.state) {
                case '팍업완료':
                    return 'success';
                case '배송중':
                    return 'primary';
                default:
                    return 'success';
            }
        },

        actionName() {
            switch (this.state) {
                case '배송중':
                    return '배송완료';
                default:
                    return '배송시작';
            }
        }
    }
}
</script>

<style scoped>

</style>
