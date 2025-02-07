-- Heroclix Card Generator
--  author Sonycrow
--  version 1.0
--  date 2020-05-15

-- Init
local b = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/' -- Necesario para encoding/decoding de BASE64
local match = string.match

-- Templates base
local _DIALTEMPLATE = "https://dialgenerator.000webhostapp.com/dial.php?token="
local _CARDTEMPLATE = "https://dialgenerator.000webhostapp.com/card.php?token="

-- Load
function onLoad()
    self.createButton({click_function = "create",  function_owner = self, label = '██', position = {-0.2, 0.15, -0.05}, rotation = {0, 15, 0}, scale = {0.1, 0.1, 0.1}, width = 800, height = 800, font_size = 400, tooltip = "Create Card"})
end

-- Create the dial
function create()
    local description = trim(self.getDescription())

    if (description ~= "") then
        local url = _CARDTEMPLATE .. enc(description)
        self.setCustomObject({ diffuse = url})
        self.setDescription("")
        self.setName("")
        self.reload()
    end
end

-- TRIM
function trim(s)
    return match(s,'^()%s*$') and '' or match(s,'^%s*(.*%S)')
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