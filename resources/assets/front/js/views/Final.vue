<template>
    <div>
        <TNav title="报到完成"></TNav>
        <div class="alert">
            点击加入群：<a href="https://jq.qq.com/?_wv=1027&k=5rrULFq">【淮师17-E8网络工作室】</a>
            <p>手动加群：573687454</p>
            <div class="ad">广告</div>
        </div>
        <group>
            <cell title="学号" :value="studentInfo.student_num"></cell>
            <cell title="姓名" :value="studentInfo.student_name"></cell>
            <cell title="班级" :value="studentInfo.department_class"></cell>
            <cell title="身份证" :value="studentInfo.id_card_with_mosaic"></cell>
            <cell title="所选宿舍" :value="studentInfo.dormitory.data.dorm_num"></cell>
        </group>
        <DormPoeple :dorm_id="String(studentInfo.dormitory.data.id)"></DormPoeple>
        <box gap="20px 20px">
            <!-- <x-button @click.native="reSelDorm" type="primary">重选宿舍</x-button> -->
            <x-button @click.native="logout" type="warn">退出</x-button>
            <x-button @click.native="$router.push({name: 'feed_back'})">意见反馈</x-button>
        </box>
    </div>
</template>
<script>
    import { Group, Cell, XButton, Box } from 'vux'
    import TNav from '../../../common/components/TNav.vue'
    import DormPoeple from '../components/DormPoeple.vue'
    export default{
        components: {
            Group, Cell, XButton, TNav, Box, DormPoeple
        },
        data () {
            return {
                studentInfo: {
                    dormitory: {
                        data: {}
                    }
                }
            }
        },
        mounted () {
            document.title = '报到完成'
            this.$http.get('me?include=dormitory').then(res => {
                this.studentInfo = res.data.data
            })
        },
        methods: {
            logout () {
                this.$http.post('auth/logout').then(res => {
                    this.$vux.toast.show({
                        text: '退出成功',
                        position: 'top',
                        type: 'text'
                    })
                    this.$router.push({name: 'index'})
                })
            },
            reSelDorm () {
                const _this = this;
                this.$vux.confirm.show({
                    title: '确认重选？',
                    content: '此操作将会取消你当前选择的宿舍，并重选。',
                    onConfirm () {
                        _this.$http.post('cancel_dorm').then(res => {
                            _this.$router.push({name: 'select_dorm'})
                        });
                    }
                })
            }
        }
    }
</script>

<style scoped lang="less">
    .alert{
        font-size: 13px;
        margin: 10px;
        color: #8a6d3b;
        background-color: #fcf8e3;
        padding: 10px;
        border: 1px solid #faebcc;
        border-radius: 4px;
        margin-top: 20px;
        line-height: 23px;
        position: relative;
        .ad{
            border: 1px solid #999;
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-size: 12px;
            line-height: 14px;
            padding: 1px;
            color: #999;
        }
    }
</style>