--	Better Notecards
--	by: Bada
--	Updated: 2017-09-15

NOTECARD = {}


-- Load and Save data

function onLoad(saved_data)

    if saved_data ~= "" then
        NOTECARD = JSON.decode(saved_data)
        if NOTECARD.description == nil then
            defaults()
        end
    else
        defaults()
    end
    createFields()
    loadData()
end

function updateSave()
    saved_data = JSON.encode(NOTECARD)
    self.script_state = saved_data
end

function onSave()
    return JSON.encode(NOTECARD)
end

function defaults()
    NOTECARD = {
        description = { "" },
        fontsize = { 250 },
        pagenumber = 1,
        maxpagenumber = 1,
        locked = false,
        input_description = 0,
        input_pagenumber = 1,
        button_lock = 0,
        button_incfont = 1,
        button_decfont = 2,
        button_pageup = 3,
        button_pagedown = 4
    }
end


-- Populate fields

function createFields()
    self.createInput({input_function = "inputDescription", function_owner = self, label = " 1. Name field controls the header\n 2. This text field controls the description\n 3. Click Arrows to change page number\n 4. Click [b]+[/b] or [b]-[/b] to change the font size\n 5. You can Lock or Unlock the Notecard\n 6. [b]BBCode[/b] also [u]works[/u][FF0000]![-]\n\n\n\n", position = {0.032, 2.5, -0.045}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.14}, width = 4500, height = 2600, font_size = 250, tooltip = "Description", alignment = 2})
    self.createInput({input_function = "inputPageNumber", function_owner = self, label = "Page Number", position = {-0.468, 2.5, -0.046}, rotation = {0, 270, 0}, scale = {0.1, 0.1, 0.1}, width = 2720, height = 310, font_size = 287, tooltip = "Page Number", alignment = 3, value = "Page Number"})

    if NOTECARD.locked then lock_label = "Locked" end
    self.createButton({click_function = "getDesc", function_owner = self, label = 'Get Desc', position = {0.368, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 1300, height = 390, font_size = 240, tooltip = "Lock / Unlock"})
    self.createButton({click_function = "setDesc", function_owner = self, label = 'Set Desc', position = {0.05, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 1300, height = 390, font_size = 240, tooltip = "Lock / Unlock"})
    self.createButton({click_function = "clickIncreaseFont", function_owner = self, label = "+", position = {-0.24, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 400, height = 390, font_size = 400, tooltip = "Increase Font Size"})
    self.createButton({click_function = "clickDecreaseFont", function_owner = self, label = "-", position = {-0.155, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 400, height = 390, font_size = 400, tooltip = "Decrease Font Size"})
    self.createButton({click_function = "clickPageUp", function_owner = self, label = "▲", position = {-0.468, 2.5, 0.286}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 340, height = 500, font_size = 300, tooltip = "Previous Page"})
    self.createButton({click_function = "clickPageDown", function_owner = self, label = "▼", position = {-0.468, 2.5, -0.378}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 340, height = 500, font_size = 300, tooltip = "Next Page"})
end

function getDesc()
    local selfDesc = self.getDescription()
    local object = getObjectFromGUID(selfDesc)
    if (object == nil) then
        print('No object found with that GUI. Please set my description to a valid GUID')
    else
        local objectDesc = object.getDescription()
        local pageNum = NOTECARD.pagenumber
        NOTECARD.description[pageNum] = objectDesc
        loadData()
        print('Get description')
    end
end

function setDesc()
    local selfDesc = self.getDescription()
    local object = getObjectFromGUID(selfDesc)
    if (object == nil) then
        print('No object found with that GUI. Please set my description to a valid GUID')
    else
        local pageNum = NOTECARD.pagenumber
        object.setDescription(NOTECARD.description[pageNum])
        print('Set Description')
    end
end

function loadData()
    local pageNum = NOTECARD.pagenumber
    if NOTECARD.description[pageNum] ~= nil then
        modifyInput(NOTECARD.input_description, NOTECARD.description[pageNum])
        modifyInput(NOTECARD.input_description, NOTECARD.fontsize[pageNum], "font_size")
    else
        addPage()
        loadData(pageNum)
    end
    updatePage()
end


-- Helpers

function modifyInput(i, v, n)
    if n then
        self.editInput({index = i, [n] = v})
    else
        self.editInput({index = i, value = v})
    end
end

function modifyButton(i, v, n)
    if n then
        self.editButton({index = i, [n] = v})
    else
        self.editButton({index = i, label = v})
    end
end


-- Page Functions

function addPage()
    NOTECARD.maxpagenumber = NOTECARD.maxpagenumber + 1
    NOTECARD.description[NOTECARD.maxpagenumber] = ""
    NOTECARD.fontsize[NOTECARD.maxpagenumber] = 250
    updateSave()
end

function removePage()
    NOTECARD.description[NOTECARD.maxpagenumber] = nil
    NOTECARD.fontsize[NOTECARD.maxpagenumber] = nil
    NOTECARD.maxpagenumber = NOTECARD.maxpagenumber - 1
    updateSave()
end

function getPageNum()
    return "" .. NOTECARD.pagenumber .. "    /    " .. NOTECARD.maxpagenumber .. ""
end

function updatePage()
    modifyInput(NOTECARD.input_pagenumber, getPageNum())
end


-- Click events

function clickLockUnlock(btn, colw)
    NOTECARD.locked = not NOTECARD.locked
    if NOTECARD.locked then
        modifyButton(NOTECARD.button_lock, "Locked")
    else
        modifyButton(NOTECARD.button_lock, "Unlocked")
    end
end

function clickIncreaseFont(btn, col)
    if NOTECARD.locked then
        alertLocked(col)
        return
    end
    NOTECARD.fontsize[NOTECARD.pagenumber] = NOTECARD.fontsize[NOTECARD.pagenumber] + 10
    updateSave()
    loadData()
end

function clickDecreaseFont(btn, col)
    if NOTECARD.locked then
        alertLocked(col)
        return
    end
    NOTECARD.fontsize[NOTECARD.pagenumber] = NOTECARD.fontsize[NOTECARD.pagenumber] - 10
    updateSave()
    loadData()
end

function clickPageUp(btn, col)
    if NOTECARD.description[NOTECARD.maxpagenumber] == "" and #NOTECARD.description > 1 then
        removePage()
    end
    if NOTECARD.pagenumber > 1 then
        NOTECARD.pagenumber = NOTECARD.pagenumber - 1
        loadData()
    end
end

function clickPageDown(btn, col)
    if NOTECARD.pagenumber == NOTECARD.maxpagenumber then
        addPage()
    end
    NOTECARD.pagenumber = NOTECARD.pagenumber + 1
    loadData()
end


-- Input events

function inputDescription(btn, col, val, sel)
    if NOTECARD.locked then
        alertLocked(col)
        return NOTECARD.description[NOTECARD.pagenumber]
    end
    NOTECARD.description[NOTECARD.pagenumber] = val
    updateSave()
end
function inputPageNumber(btn, col, val, sel)
    return getPageNum()
end


-- Misc

function alertLocked(col)
    broadcastToColor("Unable to edit. Notecard is locked.", col, {1,0,0})
end