-- Heroclix Dial Generator
--  author Sonycrow
--  version 1.0
--  date 2020-05-15

-- Init
local b = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/' -- Necesario para encoding/decoding de BASE64
local _BBCODE  = ""
local _URLDIAL = "http://dials.neobookings.loc/dial.php?token="
local _URLCARD = "http://dials.neobookings.loc/card.php?token="
local _DIALGUID = "25748c"
local _CARDGUID = ""


-- Load
function onLoad()
    self.createInput({input_function  = "inputBBCode", function_owner = self, label = "Paste here dial BBCode from http://www.hcrealms.com\n\nAnd click \"Create\" button!", position = {0, 2.5, -0.045}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.14}, width = 4800, height = 2600, font_size = 250, tooltip = "Dial BBCode", alignment = 2})
    self.createButton({click_function = "createDial",  function_owner = self, label = 'Create Dial', position = {0.368, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 1300, height = 390, font_size = 240, tooltip = "Create Dial"})
    self.createButton({click_function = "createCard",  function_owner = self, label = 'Create Card', position = {0.05, 2.5, -0.461}, rotation = {0, 180, 0}, scale = {0.1, 0.1, 0.1}, width = 1300, height = 390, font_size = 240, tooltip = "Create Card"})
end

function createDial()
    local dial = getObjectFromGUID(_DIALGUID)

    params1 = {
        diffuse = "https://sites.google.com/site/heroclixtts/figuras/daredevil015.jpg"
    }

    params2 = {
        diffuse = "https://sites.google.com/site/heroclixtts/figuras/deadpool057.jpg"
    }

    --    dial = dial.setState(1) // Si se intenta setear el estado actual falla
    --    dial.setCustomObject(params1)
    --    dial = dial.setState(2)
    --    dial.setCustomObject(params2)

    print(enc(_BBCODE))

    paramsBoard = {
        image = "http://dials.neobookings.loc/dial.php?token=" .. enc(_BBCODE)
    }

    newObject = dial.clone()
    newObject.setPosition(self.getPosition())
    newObject.setCustomObject(params1)
    newObject.reload()
end

function createCard()

end

function inputBBCode(btn, col, val, sel)
    _BBCODE = val
end


-- BASE64 encoding
function enc(data)
    return ((data:gsub('.', function(x)
        local r,b='',x:byte()
        for i=8,1,-1 do r=r..(b%2^i-b%2^(i-1)>0 and '1' or '0') end
        return r;
    end)..'0000'):gsub('%d%d%d?%d?%d?%d?', function(x)
        if (#x < 6) then return '' end
        local c=0
        for i=1,6 do c=c+(x:sub(i,i)=='1' and 2^(6-i) or 0) end
        return b:sub(c+1,c+1)
    end)..({ '', '==', '=' })[#data%3+1])
end

-- BASE64 decoding
function dec(data)
    data = string.gsub(data, '[^'..b..'=]', '')
    return (data:gsub('.', function(x)
        if (x == '=') then return '' end
        local r,f='',(b:find(x)-1)
        for i=6,1,-1 do r=r..(f%2^i-f%2^(i-1)>0 and '1' or '0') end
        return r;
    end):gsub('%d%d%d?%d?%d?%d?%d?%d?', function(x)
        if (#x ~= 8) then return '' end
        local c=0
        for i=1,8 do c=c+(x:sub(i,i)=='1' and 2^(8-i) or 0) end
        return string.char(c)
    end))
end