document.addEventListener("mouseover", (e) => {
    if (e.target.hasAttribute("tooltip")) {
        function step(el) {
            let parent = el.parentElement;
            if (!parent) return el;
            if (parent == document.body) return parent;
            if (parent.hasAttribute("tooltip-viewport")) return parent;
            return step(parent);
        }

        let target = e.target;
        let viewport = step(target);
        let timeout = (+(e.target.getAttribute("tooltip-timeout") || 100));

        let html = e.target.querySelector(".tooltip__html");
        if (!!html) {
            target.__tooltip = new CTFTooltip({
                target: target,
                viewport: viewport,
                innerHTML: html.innerHTML,
                timeout: timeout,
                top: e.target.hasAttribute("tooltip-top") ? true : false
            });
        } else {
            target.__tooltip = new CTFTooltip({
                target: target,
                viewport: viewport,
                innerText: e.target.getAttribute("tooltip"),
                timeout: timeout,
                top: e.target.hasAttribute("tooltip-top") ? true : false
            });
        }
    }
});


const SOUND_CONTEXT_OPEN = SOUND_BASE + "ui/buttonclickrelease.wav";
Creators.Actions.Sounds.precache(SOUND_CONTEXT_OPEN);

document.addEventListener("click", evContextMenu, false);
document.addEventListener("contextmenu", evContextMenu, false);

function evContextMenu(e) {
    // If click event was run we clean all context menus. (Except if target is context menu itself).
    if (e.target.classList.contains("context-menu") || hasParentWithSelector(e.target, ".context-menu")) return;
    for (let a of document.querySelectorAll(".context-menu")) a.remove();

    if (!e.target.classList.contains("contextmenu")) return;
    let html = e.target.querySelector(".contextmenu__html");
    if (!!html) {
        if (e.type == "contextmenu") e.preventDefault();
        // If dom element has contexmenu info available - we read it and apply.
        let iContext = new CTFContextMenu({
            innerHTML: html.innerHTML
        });
        document.body.appendChild(iContext.DOM);
        Creators.Actions.Sounds.play(SOUND_CONTEXT_OPEN);

        // Position this context box to be right under the cursor.
        iContext.DOM.style.left = `${e.clientX + window.scrollX}px`;
        iContext.DOM.style.top = `${e.clientY + window.scrollY}px`;
    }
}

document.addEventListener("mouseout", (e) => {
    if (e.target.hasAttribute("tooltip")) {
        e.target.__tooltip.closeAndKill();
        e.target.__tooltip = null;
    }
});

class CTFBaseOverlayElement {
    constructor(data) {}
    open() {
        this.DOM.style.opacity = 1;
    }
    close() {
        this.DOM.style.animation = '';
        this.DOM.style.opacity = 0;
    }
    closeAndKill() {
        this.cancelled = true;
        this.close();
        setTimeout(() => {
            this.DOM.remove();
        }, 200);
    }
}

class CTFTooltip extends CTFBaseOverlayElement {
    constructor(data) {
        super(data);
        this.DOM = document.createElement("div");
        this.DOM.object = this;

        this.target = data.target;
        this.viewport = data.viewport;

        this.innerText = data.innerText;
        this.innerHTML = data.innerHTML;
        this.top = data.top;

        this.timeout = data.timeout || 0;

        this.cancelled = false;

        this.DOM.addEventListener("mouseover", (e) => {
            this.DOM.style.pointerEvents = "none";
            this.closeAndKill();
        })
        this.DOM.className = 'tooltip__element';
        if (!!this.innerHTML) this.DOM.innerHTML = this.innerHTML;
        else if (!!this.innerText) this.DOM.innerHTML = `<div class="tooltip__element__textonly">${this.innerText}</div>`;
        setTimeout(() => {
            if (this.cancelled) return;
            this.viewport.append(this.DOM);

            let posX = window.scrollX + this.target.getBoundingClientRect().left + (-this.DOM.offsetWidth / 2) + (this.target.offsetWidth / 2);
            let posY = 0;
            if (this.top === true) {
                posY = window.scrollY + this.target.getBoundingClientRect().top - 5 - this.DOM.offsetHeight;
            } else {
                posY = window.scrollY + this.target.getBoundingClientRect().top + 5 + this.target.offsetHeight;
            }

            if(posX < 0) posX = 0;
            if((posX + this.DOM.offsetWidth) > window.innerWidth) posX = window.innerWidth - this.DOM.offsetWidth;

            this.DOM.style.left = posX + "px";
            this.DOM.style.top = posY + "px";

            this.open();
        }, this.timeout);

        setInterval(() => {
            // Autokill check
            if (!document.body.contains(this.target)) {
                this.closeAndKill();
            }
        }, 500);
    }
}

function hasParentWithSelector(target, selector) {
    return [...document.querySelectorAll(selector)].some(el =>
        el !== target && el.contains(target)
    )
}

document.addEventListener("mousedown", (e) => {
    // If mousedown event was run we clean all context menus. (Except if target is context menu itself).
    if (e.target.classList.contains("context-menu") || hasParentWithSelector(e.target, ".context-menu")) return;
    for (let a of document.querySelectorAll(".context-menu")) a.remove();
}, false);

class CTFContextMenu extends CTFBaseOverlayElement {
    constructor(data) {
        super(data);
        this.DOM = document.createElement("div");
        this.DOM.object = this;
        this.DOM.className = "context-menu";
        this.DOM.innerHTML = data.innerHTML;
    }
}
