function makeTableResponsive(tableElement) {
    var e,
        n = 1 < arguments.length && void 0 !== arguments[1] ? arguments[1] : {},
        t = 2 < arguments.length && void 0 !== arguments[2] ? arguments[2] : 400;

    function responsive() {
        window.innerWidth < t ? function () {
            if (!undefine) {
                var activeElement = document.activeElement.id;
                if (undefine = collapseTableToList(tableElement, n),
                    divElement.appendChild(undefine),
                    tableElement.parentElement.removeChild(tableElement),
                    // displayContent("The data for " + getName(tableElement) + " is now being rendered as a list."),
                    activeElement) {
                    var t = document.querySelector("#" + activeElement);
                    t && t.focus();
                }
            }
        }() : function () {
            if (undefine) {
                var activeElement = document.activeElement.id;
                if (divElement.removeChild(undefine),
                    divElement.appendChild(tableElement),
                    undefine = null,
                    // displayContent("The data for " + getName(tableElement) + " is now being rendered as a table."),
                    activeElement) {
                    var t = document.querySelector("#" + activeElement);
                    t && t.focus();
                }
            }
        }()
    }

    var undefine = void 0,
        wrapperElement = document.createElement("div");

    wrapperElement.classList.add("responsive-table-wrapper");

    var divElement = document.createElement("div");

    function displayContent(content) {
        divElement.innerText = content
    }

    return divElement.setAttribute("role", "alert"),
        divElement.setAttribute("aria-live", "polite"),
        divElement.classList.add("visuallyhidden"),
        wrapperElement.appendChild(divElement),
        tableElement.parentElement.insertBefore(wrapperElement, tableElement),
        wrapperElement.appendChild(tableElement),
        window.addEventListener("resize", function () {
            e || (e = setTimeout(function () {
                e = null, responsive()
            }, 66))
        }), responsive(), tableElement

}

function getName(e) {
    var t = e.querySelector("caption");
    return t ? t.innerText : e.getAttribute("aria-label") || "unnamed table"
}

function collapseTableToList(e, t) {
    var r = t.labelColumns,
        u = void 0 === r ? [] : r,
        n = t.labelFunction,
        l = void 0 === n ? function () {
            return "Row:"
        } : n,
        i = document.createElement("div");

    i.classList.add("deque");
    i.classList.add("responsive-table-list-holder");


    // var a = document.createElement("h3");
    // a.innerText = getName(e),
    //     i.appendChild(a);

    var c = Array.from(e.querySelectorAll('th')),
        o = Array.from(e.querySelectorAll("tbody tr")),
        ulElement = document.createElement("ul");

    return ulElement.classList.add("collapsed-table"),
        ulElement.setAttribute("data-num-columns", c.length),
        o.reduce(function (r, t) {
            var n = [];
            u.forEach(function (e) {
                n.push(t.children[e])
            });

            var e = l.apply(null, n),
                liElement = document.createElement("li"),
                spanElement = document.createElement("span");

            e = document.querySelector("h4.page-title").innerText;

            spanElement.classList.add("collapsed-table-header");
            spanElement.innerHTML = e.outerHTML ? e.outerHTML : e;
            liElement.appendChild(spanElement);

            var o = document.createElement("ul");
            return o.classList.add("collapsed-table-content"),

                t.querySelectorAll("th, td").forEach(function (e, i) {
                    var t = [];
                    -1 === u.indexOf(i) && t.push({
                        cell: e,
                        label: c[i].innerHTML
                    }),
                    0 < t.length && t.forEach(function (e) {
                        var t = document.createElement("li");
                        t.setAttribute("data-table-column-index", i);
                        t.setAttribute("data-id", $(e.cell).parent('tr').attr('data-id'));
                        var r = document.createElement("span");
                        r.innerHTML = e.label;
                        r.querySelectorAll('.table-sorting.sorting').forEach(function (x, y) {
                            x.classList.remove('table-sorting', 'sorting');
                            x.setAttribute("href", "#");
                        });
                        t.appendChild(r);

                        var n = document.createElement("div");
                        n.innerHTML = e.cell.innerHTML,
                            t.appendChild(n),
                            o.appendChild(t)
                    }), r.appendChild(liElement)
                }),
            0 < o.children.length && liElement.appendChild(o), r
        }, ulElement),
        i.appendChild(ulElement),
        i
}
