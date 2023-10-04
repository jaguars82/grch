import { defineStore } from "pinia"

export const useSecondaryFilter = defineStore('SecondaryFilterStore', {
    state: () => {
        return {
            deal_type: null,
            category: null,
            price: { min: null, max: null},
            agency: null,
            statusLabel: null,
            rooms: [],
            area: { min: null, max: null},
            district: null,
            street: null,
            floor: { min: null, max: null},
            totalFloors: { min: null, max: null},
            kitchenArea: { min: null, max: null},
            livingArea: { min: null, max: null},
            balconyAmount: { min: null, max: null},
            loggiaAmount: { min: null, max: null},
            windowviewStreet: false,
            windowviewYard: false,
            panoramicWindows: false,
            builtYear: { min: null, max: null},
            concierge:false,
            rubbishChute: false,
            gasPipe: false,
            closedTerritory: false,
            playground: false,
            undergroundParking: false,
            groundParking: false,
            openParking: false,
            multilevelParking: false,
            barrier: false,
        }
    }
})