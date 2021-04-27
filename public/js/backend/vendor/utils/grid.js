(function () {
  function OneLogGrid() {
    let delay = (function () {
        let timer = 0;
        return function (callback, ms) {
          clearTimeout(timer);
          timer = setTimeout(callback, ms);
        };
      })(),
      selectedItem = [],
      inputSelected = $(".selected_item"),
      selectedToolbar = $(".selected-toolbar"),
      selectedCount = $("#selected_item_count"),
      toolbar = $(".toolbar");

    this._ajaxSearch = function _ajaxSearch(
      container,
      element,
      isLoading = true,
      isGenerateHeader = false
    ) {
      let self = this,
        pageInfo = container.find("#list_info"),
        e = $(element),
        params = {},
        table = container.find("#table-scroll"),
        head = container.find("#head_content"),
        contentContainer = container.find("#body_content"),
        pageContainer = container.find("#paginate_content"),
        perPage = container.find(".range-per-page").val(),
        sortField = container.find(".sort_field").val(),
        sortType = container.find(".sort_type").val(),
        backUrlKey = container.find("#back_url_key").val(),
        filterValues = container.find(".filter-index"),
        cbFilter = container.find('select.select2').attr('name'),
        cbValue = container.find('select.select2').val();

      params._s("per_page", perPage);
      params._s("sort_field", sortField || $("#sort_field").val());
      params._s("sort_type", sortType || $("#sort_type").val());
      params._s("back_url_key", backUrlKey);
      params._s("selected_item", selectedItem.join());
      params._s("is_generate_header", isGenerateHeader);
      if (cbFilter && !(cbFilter.split('_').includes("eq"))) {
        params._s(cbFilter + '_eq', cbValue);
      }

      filterValues.each(function () {
        let el = $(this),
          name = el.attr("name"),
          value = el.val();

        if (el.hasClass("datepicker") && value !== "") {
          let from = value.split("-"),
            range = value.split("~");
          value =
            from.length === 3 ? from[2] + "-" + from[1] + "-" + from[0] : "";
          if (range.length === 2) {
            value = el.val();
          }
        }

        params._s(name, value);
      });

      if (e.hasClass("page-link")) {
        params._s("page", e.data("page"));
      }

      var $ignore = true;
      if (isLoading) {
        sendRequest(
          {
            url: pageInfo.data("url"),
            type: "GET",
            data: params,
          },
          function (response) {
            if (!response.ok) {
              return showErrorFlash(response.message);
            }
            table.find(".empty-box").remove();
            if (isGenerateHeader && response.data.head) {
              $ignore = false;
              head.html(response.data.head);
            }
            contentContainer.html(response.data.content);
            if (response.data.groupData) {
              $("#group-bar span.counter").each(function (index, item) {
                var status = $(item).data("status");
                var field = $(item).data("field") || 'status';
                var val = 0;
                var temp = response.data.groupData.find((p) => p[field] == status);
                if (temp) {
                  val = temp.total;
                }
                $(item).text(val);
              });
            }

            pageContainer
              .children("div:first")
              .html(response.data.paginator_info);
            pageContainer.children("div:eq(1)").html(response.data.paginator);

            self._activeAjaxSearch(container, $ignore);
          }
        );
      } else {
        sendRequestNotLoading(
          {
            url: pageInfo.data("url"),
            type: "GET",
            data: params,
          },
          function (response) {
            if (!response.ok) {
              return showErrorFlash(response.message);
            }
            table.find(".empty-box").remove();
            if (isGenerateHeader && response.data.head) {
              $ignore = false;
              head.html(response.data.head);
            }
            contentContainer.html(response.data.content);
            if (response.data.groupData) {
                $("#group-bar span.counter").each(function (index, item) {
                  var status = $(item).data("status");
                  var val = 0;
                  var temp = response.data.groupData.find((p) => p.status == status);
                  if (temp) {
                    val = temp.total;
                  }
                  $(item).text(val);
                });
              }
            pageContainer
              .children("div:first")
              .html(response.data.paginator_info);
            pageContainer.children("div:eq(1)").html(response.data.paginator);

            self._activeAjaxSearch(container, $ignore);
          }
        );
      }
    };

    this._activeAjaxSearch = function _activeAjaxSearch(element, ignore) {
      let self = this,
        _this = $(element);

      if (!ignore) {
        _this.find("a.sorting").on("click", function (e) {
          e.preventDefault();
          let element = $(this);
          if (element.hasClass("sorting_asc")) {
            element.removeClass("sorting_asc");
            element.addClass("sorting_desc");

            _this.find(".sort_field").val(element.data("sort"));
            _this.find(".sort_type").val("desc");
          } else if (element.hasClass("sorting_desc")) {
            element.removeClass("sorting_desc");
            element.addClass("sorting_asc");

            _this.find(".sort_field").val(element.data("sort"));
            _this.find(".sort_type").val("asc");
          } else {
            _this.find(".sorting_asc").removeClass("sorting_asc");
            _this.find(".sorting_desc").removeClass("sorting_desc");

            element.addClass("sorting_asc");
            _this.find(".sort_field").val(element.data("sort"));
            _this.find(".sort_type").val("asc");
          }

          self._ajaxSearch(_this, this);
        });

        _this.find(".filter-index").each(function (e) {
          let el = $(this);
          if (el.is("input")) {
            self._activeInputText(el, _this);
          }
          if (el.is("select")) {
            self._activeSelect(el, _this);
          }
        });
      }

      _this.find("select.range-per-page.ajax-search").on("change", function () {
        self._ajaxSearch(_this, this, true);
      });

      _this.find("a.page-link").on("click", function (e) {
        e.preventDefault();
        self._ajaxSearch(_this, this);
      });
      _this.find("a.delete-action").on("click", function (e) {
        e.preventDefault();
        let item = $(this),
          href = item.data("action"),
          name = item.parents("tr").children("td[data-name=true]"),
          spanDelete = $("#del-confirm .modal-body span");
        if (typeof name !== "undefined") {
          spanDelete.html("");
          spanDelete.append(
            name
              .map(function (index, value) {
                return $(value).text().trim();
              })
              .get()
              .join("-")
          );
        }
        $("#del_form").attr("action", href);
      });
      $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
      $("#table-scroll .delete-action").tooltip({ trigger: "hover" });

      var header = $("table thead tr.active > th a").map((index, a) =>
        $(a).text()
      );

      $(document).on("click", ".btn.btn-view-more", function (e) {
        e.preventDefault();
        $(this).parent("ul.preview").find("li.view-more").toggleClass("more");
        $(this).parent("li").addClass("view-more");
      });
    };

    this._activeInputText = function _activeInputText(element, container) {
      let self = this;
      element.on("keyup", function (e) {
        if (
          (e.keyCode >= 48 && e.keyCode <= 57) ||
          (e.keyCode >= 65 && e.keyCode <= 90) ||
          (e.keyCode >= 96 && e.keyCode <= 105) ||
          e.keyCode === 8 ||
          e.keyCode === 32
        ) {
          delay(function () {
            self._ajaxSearch(container);
          }, 800);
        }
      });

      if (element.hasClass("datepicker")) {
        element.on("dp.change", function (e) {
          delay(function () {
            self._ajaxSearch(container);
          }, 800);
        });
        element.on("apply.daterangepicker", function (e, picker) {
          $(this).val(
            picker.startDate.format("DD/MM/YYYY") +
              "~" +
              picker.endDate.format("DD/MM/YYYY")
          );
          delay(function () {
            self._ajaxSearch(container);
          }, 800);
        });
      }
    };

    this._activeSelect = function _activeSelect(element, container) {
      let self = this;
      element.on("change", function () {
        delay(function () {
          self._ajaxSearch(container);
        }, 800);
      });
    };

    OneLogGrid.prototype._search = function _search() {
      let self = this;
      $(".list-ajax").each(function () {
        self._activeAjaxSearch(this);
      });
    };

    // Cho phép sắp xếp các cột trang bảng
    this._sortColumn = function _sortColumn() {
      //Sắp xếp các cột
      $(".column-config .config-popup .btn-config-group").sortable();
    };

    // Cập nhật giá trị
    this._updateHeaderConfig = function _updateHeaderConfig() {
      let self = this;

      sendRequest(
        {
          url: $("#list_info").data("url_head"),
          type: "GET",
          data: {},
        },
        function (response) {
          if (!response.ok) {
            return showErrorFlash(response.message);
          }
          $("#head_content").html(response.data.content);
          //Reload event for html
          let listAjax = $(".list-ajax");
          listAjax.each(function () {
            self._activeAjaxSearch(this);
          });
          $(".datepicker").datetimepicker({
            format: "DD-MM-YYYY",
            locale: "vi",
            useCurrent: false,
          });
          self._ajaxSearch(listAjax, null, true, true);
          let resizing = false,
            thHeight = $("#table-scroll table th:first").height();

          $("#table-scroll table th").resizable({
            handles: "e",
            minHeight: thHeight,
            maxHeight: thHeight,
            minWidth: 50,
            resize: function (event, ui) {
              if (!resizing) {
                resizing = true;
                let menu = $(".btn-config-group"),
                  config = [];
                menu.find('input[type="checkbox"]').each(function (index) {
                  let nameValue = $(this).data("name"),
                    shownValue = $(this).is(":checked"),
                    widthValue = $(
                      "#head_content th[name=" + nameValue + "]"
                    ).outerWidth();
                  config.push({
                    name: nameValue,
                    shown: shownValue,
                    sort_order: index,
                    width: widthValue,
                  });
                });
                let table_id = $("#table_id").val();
                var object = { table_id: table_id, config: config };
                sendRequestNotLoading(
                  {
                    url: urlSaveColumnConfig,
                    type: "POST",
                    data: JSON.stringify(object),
                  },
                  function () {
                    resizing = false;
                  }
                );
              }
            },
          });
        }
      );
    };

    // Tạo bảng responsive theo kích cỡ màn hình
    this._generateResponsiveTable = function _generateResponsiveTableBaseWidth() {
      var table = $("#table-scroll table")[0];
      var self = this;
      if (table) {
        if (window.innerWidth > 480) {
          var thHeight = $("#table-scroll table th:first").height();
          var resizing = false;
          $("#table-scroll table th").resizable({
            handles: "e",
            minHeight: thHeight,
            maxHeight: thHeight,
            minWidth: 50,
            resize: function (event, ui) {
              if (!resizing) {
                resizing = true;
                var menu = $(".btn-config-group");
                var config = [];
                menu.find('input[type="checkbox"]').each(function (index) {
                  var nameValue = $(this).data("name");
                  var shownValue = $(this).is(":checked");
                  var widthValue = $(
                    "#head_content th[name=" + nameValue + "]"
                  ).outerWidth();
                  config.push({
                    name: nameValue,
                    shown: shownValue,
                    sort_order: index,
                    width: widthValue,
                  });
                });
                var table_id = $("#table_id").val();
                var object = { table_id: table_id, config: config };
                sendRequestNotLoading(
                  {
                    url: urlSaveColumnConfig,
                    type: "POST",
                    data: JSON.stringify(object),
                  },
                  function (response) {
                    resizing = false;
                  }
                );
              }
            },
          });
        } else {
          makeTableResponsive(table, {}, 480);
        }
      }
    };

    // Xử lý config column
    this._processConfigTableColumn = function _processConfigTableColumn() {
      let self = this,
        btnContain = $(".btn-config-toggle"),
        popupConfig = $(".config-popup");
      btnContain.on("click", function (event) {
        event.preventDefault();
        btnContain.toggleClass("on");
        popupConfig.toggle();
      });
      $("#config-submit,#config-reset").click(function (e) {
        e.preventDefault();
        btnContain.toggleClass("on");
        popupConfig.toggle();
        let menu = $(".btn-config-group"),
          config = [];
        let sortField = "";
        let sortType = "";
        let pageSize = "";
        if (e.currentTarget.id === "config-reset") {
          config = "";
          $("#sort_field").val("id");
          $("#sort_type").val("desc");
          $("#config_page_size").val(50);
        } else {
          menu.find('input[type="checkbox"]').each(function (index) {
            let nameValue = $(this).data("name"),
              shownValue = $(this).is(":checked"),
              widthValue = $(
                "#head_content th[name=" + nameValue + "]"
              ).outerWidth();
            config.push({
              name: nameValue,
              shown: shownValue,
              sort_order: index,
              width: widthValue,
            });
          });
        }
        sortField = $("#sort_field").val();
        sortType = $("#sort_type").val();
        pageSize = $("#config_page_size").val();

        let table_id = $("#table_id").val(),
          object = {
            table_id: table_id,
            config: config,
            sort_field: sortField,
            sort_type: sortType,
            page_size: pageSize,
          };
        sendRequestNotLoading(
          {
            url: urlSaveColumnConfig,
            type: "POST",
            data: JSON.stringify(object),
          },
          function (response) {
            if (response.ok) {
              $(".range-per-page").val(pageSize);
              let listAjax = $(".list-ajax");
              listAjax.each(function () {
                self._activeAjaxSearch(this);
              });
              $(".datepicker").datetimepicker({
                format: "DD-MM-YYYY",
                locale: "vi",
                useCurrent: false,
              });
              self._ajaxSearch(listAjax, null, true, true);

              let resizing = false,
                thHeight = $("#table-scroll table th:first").height();

              $("#table-scroll table th").resizable({
                handles: "e",
                minHeight: thHeight,
                maxHeight: thHeight,
                minWidth: 50,
                resize: function (event, ui) {
                  if (!resizing) {
                    resizing = true;
                    let menu = $(".btn-config-group"),
                      config = [];
                    menu.find('input[type="checkbox"]').each(function (index) {
                      let nameValue = $(this).data("name"),
                        shownValue = $(this).is(":checked"),
                        widthValue = $(
                          "#head_content th[name=" + nameValue + "]"
                        ).outerWidth();
                      config.push({
                        name: nameValue,
                        shown: shownValue,
                        sort_order: index,
                        width: widthValue,
                      });
                    });
                    let table_id = $("#table_id").val();
                    object = { table_id: table_id, config: config };
                    sendRequestNotLoading(
                      {
                        url: urlSaveColumnConfig,
                        type: "POST",
                        data: JSON.stringify(object),
                      },
                      function () {
                        resizing = false;
                      }
                    );
                  }
                },
              });
            }
          }
        );
      });
    };

    //Bổ sung tính năng lọc trên grid
    this._chooseFilterOperator = function () {
      var self = this;
      $(".dropdown-menu.filter-operation a").click(function (e) {
        e.preventDefault();
        var operations = {
          gt: ">",
          gteq: ">=",
          lt: "<",
          lteq: "<=",
          eq: "=",
          neq: "!=",
          in: "*!",
          ncons: "!",
          consf: "+",
          consl: "-",
          cons: "*",
          range: "<>",
        };
        var description = $(this).text().trim().split(":")[0];
        var operation = Object.keys(operations).find(
          (key) => operations[key] === description
        );

        var input = $(this).parents(".input-group").find(".filter-index");

        var filed_name = input
          .attr("name")
          .substring(0, input.attr("name").lastIndexOf("_"));
        input.attr("name", filed_name + "_" + operation);

        let inputGroup = $(this).parents(".input-group");
        inputGroup.find("button").html(description);
        if (description == "<>") {
          inputGroup.find("input").data("DateTimePicker").destroy();
          inputGroup.find("input").daterangepicker({
            opens: "center",
            autoApply: true,
            startDate: false,
            autoUpdateInput: false,
            locale: {
              format: "DD/MM/YYYY",
              separator: "~",
            },
          });
        } else {
          let data = inputGroup.find("input").data("daterangepicker");
          if (typeof data != "undefined") {
            inputGroup.find("input").data("daterangepicker").remove();
            inputGroup.find("input").datetimepicker({
              format: "DD-MM-YYYY",
              locale: "vi",
              useCurrent: false,
            });
            inputGroup.find("input").val("");
          }
        }
      });
    };

    //Bổ sung tính năng clone bản ghi
    this._displayContextMenu = function () {
      $(".table-scroll tbody").on("contextmenu", "tr", function (e) {
        let top = e.pageY - 43,
          left = e.pageX - 60;
        $("#context-menu")
          .css({
            display: "block",
            position: "absolute",
            top: top,
            left: left,
          })
          .data("id", $(this).data("id"))
          .addClass("show");

        return false; //blocks default Webbrowser right click menu
      });
    };

    //Xử lý hiện tooltip khi cột quá dài
    this._displayToolipCell = function () {
      $(".table-scroll tbody td:not(:nth-child(2))").each(function () {
        if ($(this)[0].scrollWidth > $(this).innerWidth()) {
          $(this).popover({
            content: $(this).html(),
            trigger: "hover",
            placement: "top",
            container: "body",
            html: true,
          });
        }
      });
    };

    //Xử lý từng event khi click vào menu phải
    this._processContextMenuClickEvent = function () {
      let self = this;
      $("#context-menu a").on("click", function (e) {
        e.preventDefault();
        $(this).parent().removeClass("show").hide();
        let id = $(this).parent().data("id");
        if ($(this).hasClass("view")) {
          let url = $(this)
            .attr("href")
            .replace("#", "")
            .replace(new RegExp("/[0]", "g"), "/" + id);
          sendRequest(
            {
              url: url,
              type: "GET",
            },
            function (response) {
              if (!response.ok) {
                return showErrorFlash(response.message);
              }
              self._loadDetail($(this).parent(), response.data.content);
            }
          );
        } else if ($(this).hasClass("delete")) {
          let deleteItem = function (item) {
            let href = item.data("action"),
              name = item.parents("tr").children("td[data-name=true]"),
              spanDelete = $("#del-confirm .modal-body span");
            if (typeof name !== "undefined") {
              spanDelete.html("");
              spanDelete.append(
                name
                  .map(function (index, value) {
                    return $(value).text().trim();
                  })
                  .get()
                  .join("-")
              );
            }
            $("#del_form").attr("action", href);
          };

          deleteItem($("tr[data-id=" + id + "] a.delete-action"));
          $("#del-confirm").modal();
        } else {
          location.href = $(this)
            .attr("href")
            .replace("#", "")
            .replace(new RegExp("/0/", "g"), "/" + id + "/");
        }
      });
    };

    //Click button đóng xem chi tiết
    this._clickDetailCloseBtn = function () {
      let self = this;
      $("#detail_panel_close").on("click", function () {
        self._hidePopup();
      });
    };

    // Đóng xem chi tiết
    this._hidePopup = function () {
      $("#detail-panel").removeClass("active");
      $(".collapse-view").hide();
      window.location.hash = "";
      window.history.pushState("", document.title, window.location.pathname);
      $("#modal_show").find("#sub_back_url_key").remove();
    };

    //Bổ sung tính năng xem nhanh
    this._viewDetailItem = function () {
      let self = this;
      if (window.location.hash) {
        let id = parseInt(window.location.hash.substr(1));
        delay(function () {
          self._sendRequest(id);
        }, 500);
      }

      $(document).on("click", ".detail-toggle", function (e) {
        e.preventDefault();
        let _this = $(this),
          parent = _this.closest("tr");
        if (parent.length === 0) {
          parent = _this.closest("li");
        }
        let id = parent.data("id");
        window.location.hash = id;

        self._sendRequest(id);
      });
    };

    // Gửi request lấy thông tin view
    this._sendRequest = function (id) {
      let self = this,
        backUrlKey = $("#back_url_key").val(),
        url = window.location.pathname + "/" + id;
      sendRequest(
        {
          url: url,
          type: "GET",
          data: {
            back_url_key: backUrlKey,
            grid: true,
          },
        },
        function (response) {
          if (!response.ok) {
            return showErrorFlash(response.message);
          }
          self._loadDetail(response.data, url);
          if (typeof detailCallback === "function") {
            detailCallback(id);
          }
        }
      );
    };

    // Load thông tin chi tiết của item
    this._loadDetail = function (data, url) {
      $("#detail-panel .header-detail-panel").html(data.title);

      $("#detail-panel").data("url", url).addClass("active");
      $("#divDetail").html("").append(data.content);
      this._registerAuditing();
      this._registerOverwidthTitle();
      $("#modal_show")
        .find(".modal-content")
        .append(
          $("<input>")
            .attr("type", "hidden")
            .attr("id", "sub_back_url_key")
            .val(data.backUrlKey)
        );
    };

    //Bổ sung tính năng xem nhanh
    this._dbclickRow = function () {
      let self = this,
        table = $("#main-table"),
        disable = table.data("disable-db-click");
      if (disable) return;
      $(document).on("dblclick", "#main-table tbody tr", function (e) {
        e.preventDefault();
        const $this = $(this),
          id = $this.data("id"),
          dbclick = $this.data("dbclick");

        if (dbclick == "off") return;
        window.location.hash = id;
        self._sendRequest(id);
      });
    };

    // Xử lý sự kiện ấn nút hiển thị lịch sử chỉnh sửa
    this._registerAuditing = function () {
      let buttonAuditing = $("#showAuditing"),
        collapseAuditing = $("#collapseAuditing"),
        url = buttonAuditing.data("url");

      if (buttonAuditing.length === 0) {
        return false;
      }

      buttonAuditing.on("click", function () {
        if (collapseAuditing.hasClass("show")) {
          collapseAuditing.collapse("hide");
          return false;
        }

        sendRequest(
          {
            url: url,
            type: "GET",
          },
          function (response) {
            if (!response.ok) {
              return showErrorFlash(response.message);
            }
            let content = response.data.content;
            collapseAuditing.find(".card-body").html("").append(content);
            collapseAuditing.collapse("show");
          }
        );
      });
    };

    (this._registerOverwidthTitle = function () {
      setTimeout(() => {
        $(".view-control.form-control").each(function () {
          if ($(this)[0].scrollWidth > $(this).innerWidth()) {
            $(this)
              .closest(".edit-group-control")
              .popover({
                content: $(this).val(),
                trigger: "hover",
                placement: "bottom",
                container: "body",
              });
          }
        });
      }, 0);
    }),
      (this._selectedItem = function () {
        let selectedIds = inputSelected.val();
        if (typeof selectedIds != "undefined") {
          selectedItem = selectedIds != "" ? selectedIds.split(",") : [];
        }

        $(document).on("click", ".checkbox-single", function () {
          let checkbox = $(this),
            tr = checkbox.closest("tr"),
            id = tr.data("id"),
            checked = checkbox.is(":checked");

          if (!id) return;
          let uniqueSelected =
            selectedItem.length > 0 ? [...new Set(selectedItem)] : [];
          if (checked) {
            uniqueSelected.push(id);
            selectedToolbar.css("display", "flex");
            selectedCount.find("span").text(uniqueSelected.length);
            toolbar.css("display", "none");
            if (!tr.hasClass("row-selected")) tr.addClass("row-selected");
          } else {
            let index = uniqueSelected.indexOf(id);
            if (index > -1) {
              uniqueSelected.splice(index, 1);
            }

            if (uniqueSelected.length > 0) {
              selectedCount.find("span").text(uniqueSelected.length);
            } else {
              selectedToolbar.css("display", "none");
              toolbar.css("display", "flex");
            }
            tr.removeClass("row-selected");
          }
          selectedItem = uniqueSelected;
          inputSelected.val(selectedItem.join(","));
        });
      }),
      // Xử lý sự kiến check all
      (this._selectAllItem = function () {
        $(document).on("click", ".checkbox-all", function (e) {
          let table = $(e.target).closest("table"),
            uniqueSelected =
              selectedItem.length > 0 ? [...new Set(selectedItem)] : [];

          $("td input:checkbox", table).prop("checked", this.checked);
          if (this.checked) {
            selectedToolbar.css("display", "flex");
            toolbar.css("display", "none");
            $(this)
              .parents("table")
              .find("tbody tr")
              .each(function () {
                let tr = $(this),
                  id = tr.data("id");

                if (!uniqueSelected.includes(id)) uniqueSelected.push(id);
                if (!tr.hasClass("row-selected")) tr.addClass("row-selected");
              });
            selectedCount.find("span").text(uniqueSelected.length);
          } else {
            $(this)
              .parents("table")
              .find("tbody tr")
              .each(function () {
                let tr = $(this),
                  id = tr.data("id"),
                  index = uniqueSelected.indexOf(id);

                if (index > -1) {
                  uniqueSelected.splice(index, 1);
                }
                tr.removeClass("row-selected");
              });

            if (uniqueSelected.length > 0) {
              selectedCount.find("span").text(uniqueSelected.length);
            } else {
              selectedToolbar.css("display", "none");
              toolbar.css("display", "flex");
            }
            selectedCount.find("span").text(uniqueSelected.length);
          }
          selectedItem = uniqueSelected;
          inputSelected.val(selectedItem.join(","));
        });
      }),
      (this._unselectedAll = function () {
        $(document).on("click", ".unselected-all-btn", function () {
          selectedToolbar.css("display", "none");
          toolbar.css("display", "flex");
          selectedItem = [];
          inputSelected.val("");
          $(".checkbox-all").prop("checked", false);

          $("#body_content")
            .find("tr.row-selected")
            .each(function () {
              let row = $(this);
              row.removeClass("row-selected");
              row.find(".checkbox-single").prop("checked", false);
            });
        });
      }),
      (this.init = function () {
        this._search();
        this._sortColumn();
        this._generateResponsiveTable();
        this._processConfigTableColumn();
        this._chooseFilterOperator();
        this._displayContextMenu();
        this._displayToolipCell();
        this._processContextMenuClickEvent();
        this._clickDetailCloseBtn();
        this._viewDetailItem();
        this._dbclickRow();
        this._selectedItem();
        this._selectAllItem();
        this._unselectedAll();
      });
    // this.init();
  }

  $(document).on("click", function (e) {
    let container = $("#context-menu");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.removeClass("show").hide();
    }
  });

  if (
    typeof define === "function" &&
    typeof define.amd === "object" &&
    define.amd
  ) {
    // AMD. Register as an anonymous module.
    define(function () {
      return OneLogGrid;
    });
  } else if (typeof module !== "undefined" && module.exports) {
    module.exports = OneLogGrid.attach;
    module.exports.OneLogGrid = OneLogGrid;
  } else {
    window.OneLogGrid = OneLogGrid;
  }
})();
