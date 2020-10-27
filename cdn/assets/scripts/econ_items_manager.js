function CItem_Equip(iIndex, sClass, sSlot) {
    return new Promise((rs, rj) => {
        Creators.Actions.API.send("/api/IUsers/GLoadout", {
                method: "POST",
                data: {
                    class: sClass,
                    index: iIndex,
                    slot: sSlot
                }
            })
            .then(d => {
                if (d.result == "SUCCESS") {
                    rs(d);
                } else {
                    rj(d);
                }
            })
            .catch(rj);
    });
}

function CItem_Use(iItem, iTarget) {
    return new Promise((rs, rj) => {
        Creators.Actions.API.send("/api/IEconomyItems/GManageItem", {
                method: "POST",
                data: {
                    item_id: iItem,
                    target: iTarget
                }
            })
            .then(d => {
                if (d.result == "SUCCESS") {
                    rs(d);
                } else {
                    rj(d);
                }
            })
            .catch(rj);
    });
}

function CItem_Delete(iIndex) {
    return new Promise((re, rj) => {
        Creators.Actions.API.send('/api/IEconomyItems/GManageItem', {
                method: "DELETE",
                data: {
                    item_id: iIndex
                }
            })
            .then(re)
            .catch(rj)
    });
}

function CItem_Scrap(iIndex) {
    return new Promise((re, rj) => {
        Creators.Actions.API.send('/api/IEconomyItems/GManageItem', {
                method: "PATCH",
                data: {
                    item_id: iIndex
                }
            })
            .then(re)
            .catch(rj)
    });
}

function CItem_Move(iIndex, iSlot) {
    return new Promise((re, rj) => {
        Creators.Actions.API.send("/api/IEconomyItems/GMoveItem", {
                method: "POST",
                data: {
                    item_id: iIndex,
                    slot_id: iSlot
                }
            })
            .then(re)
            .catch(rj)
    });
}
