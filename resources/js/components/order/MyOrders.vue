<template>
    <div>
        <b-card :header="state" :header-bg-variant="stateColor" header-text-variant="white" class="mb-5">
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
        <b-row>
            <b-col class="text-center">
                <b-button variant="warning">
                    <a :href="kakaoMapLink" target="_blank">카카오 맵 경로찾기</a>
                </b-button>
            </b-col>
        </b-row>

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
            kakaoMapLink: '',
        }
    },

    async mounted() {
        try {
            const response = await axios.get(`/api/order/${this.order.order_id}`);

            if (response.status === 200) {
                this.data = response.data;
                this.getGeoLocation();
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
                const response = await axios.post(`https://deliver.10tenminute.xyz/api/persist_shipment`, {
                    'order_number': this.order.order_id,
                });

                if (response.status === 200) {
                    axios.post(`/api/order/${this.order.id}/start_delivery`);
                    this.state = '배송중';
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            }
        },

        async getGeoLocation() {
            let geocoder = new kakao.maps.services.Geocoder();

            geocoder.addressSearch(this.data.delivery.address1, (result, status)  => {
                console.log(result);
                if (status === kakao.maps.services.Status.OK) {
                    this.kakaoMapLink =
                        'https://map.kakao.com/link/to/'
                        + result[0].address_name + ','
                        + result[0].y + ','
                        + result[0].x;
                }
            });
        },
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
            if (this.data.delivery.started_at === null) {
                return '배송시작';
            } else {
                return '배송완료';
            }
        }
    }
}
</script>
