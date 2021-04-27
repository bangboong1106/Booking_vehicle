
<template>
  <div style="padding: 10px">
    <a-page-header
      style="border: 1px solid rgb(235, 237, 240);margin-bottom: 1.5em"
      title="Lịch biểu"
    />
    <FullCalendar ref="fullCalendar" :options="calendarOptions" :customButtons="customButtons" :eventSources="eventSources" />
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import axios from "axios";
import moment from "moment/moment";
import EventBus from "@/event-bus";
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
  },
  created() {
    // Listening the event hello
    EventBus.$on('renderFullCalendar', this.handlerRender);
  },
  destroyed() {

    EventBus.$off('renderFullCalendar', this.handlerRender);
  },
  data() {
    return {
        calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "",
        },
          customButtons: {

            today: {
              text: "Hôm nay",
              click: () => {
                let calendarApi = this.$refs.fullCalendar.getApi();
                calendarApi.today();
              }
            }
          },
        editable: false,
        selectable: false,
        locale: "vi",
        eventSources: [
          {
            events(fetchInfo, successCallback, failureCallback) {
              axios
                .get(`c-order-customer/event`, {
                  params: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                    timezone: fetchInfo.timezone,
                  },
                })
                .then((response) => {
                  successCallback(response.data);
                });
            },
            color: "yellow",
            textColor: "black",
          },
        ],
      },
    };
  },
  methods:{
    handlerRender(){
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.refetchEvents();
    }
  }
};
</script>
