// =============================================================
// Shopy — Global Validation Engine + Alert/Confirm Helpers
// =============================================================

// -------------------------------------------------------------
// ✅ SEPARATE FUNCTION: AUTOMATIC CASE TRANSFORMATION
// -------------------------------------------------------------
function applyCaseTransformation($input, rawValue, forceCaseOption) {
    if (forceCaseOption === "upper") {
        const transformed = rawValue.toUpperCase();
        if (rawValue !== transformed) {
            $input.val(transformed);
        }
        return transformed;
    }
    if (forceCaseOption === "lower") {
        const transformed = rawValue.toLowerCase();
        if (rawValue !== transformed) {
            $input.val(transformed);
        }
        return transformed;
    }
    return rawValue;
}

// -------------------------------------------------------------
// ✅ SEPARATE FUNCTION: EVALUATE TEXT CONSTRAINTS
// -------------------------------------------------------------
function evaluateTextConstraints(rawValue, rules, $input) {
    const value = rawValue.trim();
    const minLength = parseInt($input.attr("minlength")) || rules.minLength || 0;
    const maxLength = parseInt($input.attr("maxlength")) || rules.maxLength || Infinity;

    if (minLength && value.length < minLength) {
        return {
            isValid: false,
            msg: `Minimum length should be ${minLength} characters.`,
        };
    }

    if (maxLength !== Infinity && value.length > maxLength) {
        return {
            isValid: false,
            msg: `Maximum length should be ${maxLength} characters.`,
        };
    }

    if (rules.maxConsecutive && value.length > 0) {
        const regexStr = `(.)\\1{${rules.maxConsecutive},}`;
        const repeatingRegex = new RegExp(regexStr, "i");
        if (repeatingRegex.test(value)) {
            return {
                isValid: false,
                msg: `Too many repeating continuous characters (Max allowed: ${rules.maxConsecutive}).`,
            };
        }
    }

    return { isValid: true, msg: "" };
}

function getFieldValue(name) {
    const $field = $(`[name="${name}"]`);
    if ($field.is(":checkbox")) {
        return $field.is(":checked") ? "on" : "";
    }
    return ($field.val() || "").toString().trim();
}

function checkDependency(dependsOn) {
    const parentVal = getFieldValue(dependsOn.field);
    if (typeof dependsOn.value === "function") {
        return dependsOn.value(parentVal);
    }
    return parentVal === dependsOn.value;
}

// -------------------------------------------------------------
// 🔹 MAIN VALIDATE FIELD FUNCTION
// -------------------------------------------------------------
function validateField(input) {
    const rules = validationRules[input.name];
    if (!rules) return true;

    const $input = $(input);
    let rawValue = ($input.val() || "").toString();

    if (rules.forceCase) {
        rawValue = applyCaseTransformation($input, rawValue, rules.forceCase);
    }

    const value = rawValue.trim();
    let isValid = true;
    let customMessage = rules.message || "This field is invalid.";

    // ---------------- DEPENDENCY CHECK ----------------
    let conditionMet = true;
    if (rules.dependsOn) {
        conditionMet = checkDependency(rules.dependsOn);
        if (!conditionMet) {
            $input.val("").removeClass("is-invalid is-valid");
            $input.next(".invalid-feedback").remove();
            return true;
        }
    }

    let isRequired = false;
    if (typeof rules.required === "function") {
        isRequired = rules.required();
    } else {
        isRequired = rules.required || (rules.dependsOn && conditionMet);
    }

    // ---------------- REQUIRED CHECK ----------------
    if (isRequired && !value) {
        isValid = false;
        customMessage = rules.message || "This field is required.";
    }

    // ---------------- VALIDATION CHECKS ----------------
    else if (value) {
        const constraintCheck = evaluateTextConstraints(value, rules, $input);
        if (!constraintCheck.isValid) {
            isValid = false;
            customMessage = constraintCheck.msg;
        } else {
            if (typeof rules.pattern === "function") {
                isValid = rules.pattern(value);
            } else if (rules.pattern instanceof RegExp) {
                isValid = rules.pattern.test(value);
            }
        }
    }

    // ---------------- UI UPDATE ----------------
    const $target = input._flatpickr ? $(input._flatpickr.altInput) : $input;
    $target.next(".invalid-feedback").remove();

    if (!isValid) {
        $target.addClass("is-invalid").removeClass("is-valid");
        $target.after(`<div class="invalid-feedback">${customMessage}</div>`);
    } else {
        $target.removeClass("is-invalid").addClass("is-valid");
    }

    return isValid;
}

// -------------------------------------------------------------
// 🔹 INIT & EVENT LISTENERS
// -------------------------------------------------------------
let touchedFields = new Set();
let isInitialLoad = false;

function applyInputRestrictions($input, rules) {
    if (!rules) return;
    bindInputBlocking($input, rules);

    let rawValue = ($input.val() || "").toString();

    if (rules.forceCase) {
        rawValue = applyCaseTransformation($input, rawValue, rules.forceCase);
    } else {
        rawValue = ($input.val() || "").toString();
    }

    if (rules.notAllowSpace) {
        $input.off("keydown.noSpace").on("keydown.noSpace", function (e) {
            if (e.key === " ") e.preventDefault();
        });
        $input.off("paste.noSpace").on("paste.noSpace", function (e) {
            e.preventDefault();
            const text = (e.originalEvent || e).clipboardData
                .getData("text")
                .replace(/\s+/g, "");
            document.execCommand("insertText", false, text);
        });
    }

    rawValue = applyTextTypeFilter($input, rawValue, rules);

    if (rules.maxLength && rawValue.length > rules.maxLength) {
        const truncated = rawValue.slice(0, rules.maxLength);
        if (truncated !== rawValue) {
            $input.val(truncated);
            rawValue = truncated;
        }
        $input.off("keypress.maxLength").on("keypress.maxLength", function (e) {
            if (
                rules.maxLength &&
                this.value.length >= rules.maxLength &&
                !e.ctrlKey &&
                !e.metaKey &&
                e.which !== 8 &&
                e.which !== 46
            ) {
                e.preventDefault();
            }
        });
    }

    if (rules.maxConsecutive && rawValue.length > 0) {
        const limit = Number(rules.maxConsecutive);
        if (Number.isFinite(limit) && limit >= 0) {
            const chars = rawValue.split("");
            let out = "";
            let runChar = null;
            let runCount = 0;
            for (const ch of chars) {
                if (ch === runChar) {
                    runCount += 1;
                } else {
                    runChar = ch;
                    runCount = 1;
                }
                if (runCount <= limit) out += ch;
            }
            if (out !== rawValue) $input.val(out);
        }
    }
}

function initValidation(form) {
    $(form).on("input change blur", "input, select, textarea", function () {
        touchedFields.add(this.name);
        if (!isInitialLoad) {
            const rules = validationRules[this.name];
            if (rules) applyInputRestrictions($(this), rules);
            validateField(this);
            if (typeof revalidateDependents === "function") revalidateDependents(this.name);
        }
    });
}

// -------------------------------------------------------------
// ✅ APPLY TEXT TYPE FILTER
// -------------------------------------------------------------
function applyTextTypeFilter($input, rawValue, rules) {
    if (!rules.textType) return rawValue;

    const filters = {
        numeric: /[^0-9]/g,
        alpha: /[^A-Za-z]/g,
        alphanumeric: /[^A-Za-z0-9]/g,
        text: /[^A-Za-z\s]/g,
        textWithSpecial: /[^A-Za-z0-9\s!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]/g,
        alphaWithSpecial: /[^A-Za-z\s!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]/g,
        alphanumericWithSpecial: /[^A-Za-z0-9\s!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]/g,
    };

    const filterRegex = filters[rules.textType];
    if (filterRegex) {
        const filteredValue = rawValue.replace(filterRegex, "");
        if (filteredValue !== rawValue) $input.val(filteredValue);
        return filteredValue;
    }
    return rawValue;
}

function applyCopyPasteRestriction($input, rules) {
    if (rules.allowCopyPaste === false) {
        $input.off("copy paste cut").on("copy paste cut", function (e) {
            e.preventDefault();
        });
        $input.on("keydown", function (e) {
            if ((e.ctrlKey || e.metaKey) && ["c", "v", "x"].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }
        });
    }
}

function bindInputBlocking($input, rules) {
    $input.off("beforeinput.validation").on("beforeinput.validation", function (e) {
        if (!e.originalEvent || e.originalEvent.inputType.indexOf("insert") === -1) return;
        const char = e.originalEvent.data;
        if (!char) return;
        let allowed = true;

        switch (rules.textType) {
            case "numeric":
                allowed = /^[0-9]$/.test(char);
                break;
            case "alpha":
                allowed = /^[A-Za-z]$/.test(char);
                break;
            case "text":
                allowed = /^[A-Za-z ]$/.test(char);
                break;
            case "alphanumeric":
                allowed = /^[A-Za-z0-9]$/.test(char);
                break;
            case "textWithSpecial":
                allowed = /^[A-Za-z0-9 !@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]$/.test(char);
                break;
            case "alphaWithSpecial":
                allowed = /^[A-Za-z !@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]$/.test(char);
                break;
            case "alphanumericWithSpecial":
                allowed = /^[A-Za-z0-9 !@#$%^&*()_+\-=[\]{};':"\\|,.<>/?`~]$/.test(char);
                break;
        }

        if (!allowed) e.preventDefault();
    });
}

// -------------------------------------------------------------
// 🔹 PASSWORD MATCH VALIDATION (generic)
// -------------------------------------------------------------
function validatePasswordMatch(passwordName, confirmName) {
    const pass = ($(`[name="${passwordName}"]`).val() || "").trim();
    const confirm = ($(`[name="${confirmName}"]`).val() || "").trim();
    const $confirmField = $(`[name="${confirmName}"]`);
    $confirmField.next(".invalid-feedback").remove();

    if (confirm === "") {
        $confirmField.removeClass("is-valid is-invalid");
        return true;
    }

    if (pass !== confirm) {
        $confirmField
            .addClass("is-invalid")
            .removeClass("is-valid")
            .after('<div class="invalid-feedback">Passwords do not match.</div>');
        return false;
    }
    $confirmField.removeClass("is-invalid").addClass("is-valid");
    return true;
}

// -------------------------------------------------------------
// 🔹 FORM SUBMIT VALIDATION
// -------------------------------------------------------------
function setupFormValidation(formSelector, extraChecks) {
    const $form = $(formSelector);
    if (!$form.length) return;

    initValidation(formSelector);

    // Real-time password confirmation
    $form.on("input", "[name='password_confirmation']", function () {
        validatePasswordMatch("password", "password_confirmation");
    });
    $form.on("input", "[name='password']", function () {
        if ($("[name='password_confirmation']").val()) {
            validatePasswordMatch("password", "password_confirmation");
        }
    });

    $form.on("submit", function (e) {
        let isValid = true;
        $(this).find("input, select, textarea").each(function () {
            if (!validateField(this)) isValid = false;
        });
        if (typeof extraChecks === "function") {
            if (!extraChecks()) isValid = false;
        }
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const $firstError = $form.find(".is-invalid").first();
            if ($firstError.length) {
                $("html, body").animate({ scrollTop: $firstError.offset().top - 100 }, 300);
            }
            return false;
        }
    });
}

// =============================================================
// validationRules — filled per-page via @section('scripts')
// =============================================================
// Default empty — each page overrides this before calling
// setupFormValidation()
if (typeof validationRules === "undefined") {
    var validationRules = {};
}
