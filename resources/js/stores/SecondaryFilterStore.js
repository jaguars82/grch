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
            concierge: false,
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
    },
    actions: {
        filterIsDirty: (state) => {
            if (state.deal_type !== null) return true
            if (state.category !== null) return true
            // if (state.price.min !== null || state.price.max !== null) return true
            if (state.agency !== null) return true
            if (state.statusLabel !== null) return true
            if (state.rooms.length > 0) return true
            if (state.area.min !== null || state.area.max !== null) return true
            if (state.district !== null) return true
            if (state.street !== null) return true
            if (state.floor.min !== null || state.floor.max !== null) return true
            if (state.totalFloors.min !== null || state.totalFloors.max !== null) return true
            if (state.kitchenArea.min !== null || state.kitchenArea.max !== null) return true
            if (state.livingArea.min !== null || state.livingArea.max !== null) return true
            if (state.balconyAmount.min !== null || state.balconyAmount.max !== null) return true
            if (state.loggiaAmount.min !== null || state.loggiaAmount.max !== null) return true
            if (state.windowviewStreet !== false) return true
            if (state.windowviewYard !== false) return true
            if (state.panoramicWindows !== false) return true
            if (state.builtYear.min !== null || state.builtYear.max !== null) return true
            if (state.concierge !== false) return true
            if (state.rubbishChute !== false) return true
            if (state.gasPipe !== false) return true
            if (state.closedTerritory !== false) return true
            if (state.playground !== false) return true
            if (state.undergroundParking !== false) return true
            if (state.groundParking !== false) return true
            if (state.openParking !== false) return true
            if (state.multilevelParking !== false) return true
            if (state.barrier !== false) return true
            return false
        }
    }
})