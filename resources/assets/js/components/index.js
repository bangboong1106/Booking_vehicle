import Card from "./Cards/Card.vue";
import ChartCard from "./Cards/ChartCard.vue";
import StatsCard from "./Cards/StatsCard.vue";

import FormGroupAddressAutocomplete from "./Inputs/FormGroupAddressAutocomplete";

import SidebarPlugin from "./SidebarPlugin/index";


let components = {
    Card,
    ChartCard,
    StatsCard,
    SidebarPlugin,
    FormGroupAddressAutocomplete
};

export default components;

export {
    Card,
    ChartCard,
    StatsCard,
    SidebarPlugin,
    FormGroupAddressAutocomplete
};
