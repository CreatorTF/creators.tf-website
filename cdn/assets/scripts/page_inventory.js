function CInventory_ButtonDeleteItem(index) {
    return new Promise(async (rs, rj) => {
        if (await Creators.Actions.Modals.confirm({
                name: "Are you sure?",
                innerText: "Are you sure you want to delete this item? This action can not be undone."
            })) {
            Creators.Actions.Modals.progress({
                name: "Processing...",
                innerText: "We're deleting this item."
            });

            CItem_Delete(index)
                .then(d => {
                    if (d.result == "SUCCESS") {

                        let iSlot = Inventory.slots.find(s => !!s.item && s.item.id == index);
                        if (!!iSlot) iSlot.clearItem();
                        CInventory_ParseOverflows(d.overflows);

                        (async () => {
                            await Creators.Actions.Modals.alert({
                                name: "Success!",
                                innerText: "Your item was successfully deleted."
                            });
                            rs(d);
                        })();
                    } else {
                        Creators.Actions.Modals.error({
                            name: `${d.error.code} - ${d.error.title}`,
                            innerText: d.error.content
                        });
                        rj(d);
                    }
                });
        } else {
            rj();
        }
    });
}

function CInventory_ButtonScrapItem(index, value) {
    return new Promise(async (rs, rj) => {
        if (await Creators.Actions.Modals.confirm({
                name: "Are you sure?",
                innerText: `Are you sure you want to scrap this item for <b>${value} <i class='mdi mdi-currency-usd-circle-outline'></i></b>? This action can not be undone.`
            })) {
            Creators.Actions.Modals.progress({
                name: "Processing...",
                innerText: "We're scrapping this item."
            });

            CItem_Scrap(index)
                .then(d => {
                    if (d.result == "SUCCESS") {

                        let iSlot = Inventory.slots.find(s => !!s.item && s.item.id == index);
                        if (!!iSlot) iSlot.clearItem();
                        CInventory_ParseOverflows(d.overflows);

                        (async () => {
                            await Creators.Actions.Modals.alert({
                                name: "Success!",
                                innerText: "Your item was successfully scrapped."
                            });
                            rs(d);
                        })();
                    } else {
                        Creators.Actions.Modals.error({
                            name: `${d.error.code} - ${d.error.title}`,
                            innerText: d.error.content
                        });
                        rj(d);
                    }
                });
        } else {
            rj();
        }
    });
}

function CInventory_GetSelectedItems() {
    let list = [];
    for (let slot of Inventory.slots) {
        if (slot.selected) {
            list.push(slot.item.id);
            slot.clearItem();
        }
    }
    return list;
}

function CInventory_GetSelectedScrapValue() {
    let iValue = 0;
    for (let slot of Inventory.slots) {
        if (slot.selected) {
            iValue += (+slot.item.DOM2.getAttribute("data-scrap"))
        }
    }
    return iValue;
}

function CInventory_ButtonBulkDelete() {
    return new Promise(async (rs, rj) => {
        if (await Creators.Actions.Modals.confirm({
                name: "Are you sure?",
                innerText: "Are you sure you want to delete multiple items at once? This action can not be undone."
            })) {
            Creators.Actions.Modals.progress({
                name: "Processing...",
                innerText: "We're deleting these items."
            });

            CItem_BulkDelete(CInventory_GetSelectedItems())
                .then(d => {
                    if (d.result == "SUCCESS") {
                        CInventory_ParseOverflows(d.overflows);

                        (async () => {
                            await Creators.Actions.Modals.alert({
                                name: "Success!",
                                innerText: "Your items were successfully deleted."
                            });
                            rs(d);
                        })();
                    } else {
                        Creators.Actions.Modals.error({
                            name: `${d.error.code} - ${d.error.title}`,
                            innerText: d.error.content
                        });
                        rj(d);
                    }
                });
        } else {
            rj();
        }
    });
}

function CInventory_ButtonBulkScrap() {
    return new Promise(async (rs, rj) => {
        if (await Creators.Actions.Modals.confirm({
                name: "Are you sure?",
                innerText: `Are you sure you want to scrap multiple items at once for <b>${CInventory_GetSelectedScrapValue()} <i class='mdi mdi-currency-usd-circle-outline'></i></b>? This action can not be undone.`
            })) {
            Creators.Actions.Modals.progress({
                name: "Processing...",
                innerText: "We're deleting these items."
            });

            CItem_BulkScrap(CInventory_GetSelectedItems())
                .then(d => {
                    if (d.result == "SUCCESS") {
                        CInventory_ParseOverflows(d.overflows);

                        (async () => {
                            await Creators.Actions.Modals.alert({
                                name: "Success!",
                                innerText: "Your items were successfully scrapped."
                            });
                            rs(d);
                        })();
                    } else {
                        Creators.Actions.Modals.error({
                            name: `${d.error.code} - ${d.error.title}`,
                            innerText: d.error.content
                        });
                        rj(d);
                    }
                });
        } else {
            rj();
        }
    });
}

function CItem_ButtonEquip(el, index, sClass, slot) {
    return new Promise(async (rs, rj) => {
        el.classList.add("loading");
        let d = await CItem_Equip(index, sClass, slot);
        document.location.href = d.url;
        rs();
    });
}

function CItem_ButtonUse(el, index, target) {
    return new Promise(async (rs, rj) => {
        el.classList.add("loading");
        document.location.href = `/items/use/${index}/${target}`;
        rs();
    });
}

function CItem_ButtonUseConfirm(index, target) {
    return new Promise(async (rs, rj) => {
        if (await Creators.Actions.Modals.confirm({
                name: "Are you sure?",
                innerText: `Are you sure you want to use these items? This action can not be undone.`
            })) {
            Creators.Actions.Modals.progress({
                name: "Processing...",
                innerText: "Please wait."
            });

            CItem_Use(index, target)
                .then(d => {
                    if (d.result == "SUCCESS") {

                        (async () => {
                            await Creators.Actions.Modals.alert({
                                name: "Success!",
                                innerText: "Your items were successfully used."
                            });
                            document.location.href = "/my/inv";
                            rs(d);
                        })();
                    } else {
                        Creators.Actions.Modals.error({
                            name: `${d.error.code} - ${d.error.title}`,
                            innerText: d.error.content
                        });
                        rj(d);
                    }
                });
        } else {
            rj();
        }
    });
}

function CItem_ButtonUseLootbox(lootbox) {
    return new Promise(async (rs, rj) => {
        Creators.Actions.Modals.progress({
            name: "Processing...",
            innerText: "Please wait."
        });

        CItem_Use(lootbox)
            .then(d => {
                if (d.result == "SUCCESS") {
                    Preview_ShowLootboxLoot()
                        .then(() => {
                            document.location.href = "/my/inv";
                            rs();
                        });
                } else {
                    Creators.Actions.Modals.error({
                        name: `${d.error.code} - ${d.error.title}`,
                        innerText: d.error.content
                    });
                    rj(d);
                }
            });
    });
}
