COMMANDS HELP
-------------

### Start simulator:
```
$ php dacsim.php -h 0.0.0.0 -p 5001
Aug 14 18:33:51 :: DACSIM Listening on 0.0.0.0:5001
```


### Connect to the simulator:
```
$ telnet 127.0.0.1 5001
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
```

You'll see the following in the running simulator terminal:
```
Aug 14 18:34:25 :: Connection #1 accepted!
```


### Send commands to the simulator:

In the telnet prompt enter the command in HEX and press ENTER:
```
0015.004C.02F8.0002...03.4D3131323330544537383434...EC
```

You'll get a response as follows:
```
0008004C00010001AA
```

You'll see the following in the running simulator terminal:
```
Aug 14 18:34:45 :: =Received message #1: 0015.004C.02F8.0002...03.4D3131323330544537383434...EC
-HEADER---------------------------------------
Size:      0015 (21: Valid)
Sequence:  004C (76)
Type:      02F8 (760)
CheckSum:  EC   (Valid)

-PAYLOAD--------------------------------------
Identifier Component:
BSICode:   0002 (2)
ReqType:   03 (Delete)
SerialN:   4D3131323330544537383434 (M11230TE7844)

Aug 14 18:34:45 :: =Response to message #1: 0008004C00010001AA
-HEADER---------------------------------------
Size:      0008 (8: Valid)
Sequence:  004C (76)
Type:      0001 (1)
CheckSum:  AA   (Valid)

-PAYLOAD--------------------------------------
Status:    1 (1)

--------------------------------------------------------------------------------
=END OF REQUEST #1==============================================================
--------------------------------------------------------------------------------
```

### Parse a HEX command without executing it
```
$ php dacsim.php --stream 00B2.004C.02F8.0002...01.4D3131323330544537383434..01.00000007.30303030303338343235313037313639.238E.0000..02.00000027.00000001.0000019B.00000004.00000000.00000000.00000001..03.00000003.01.06..04.000003FF.000003E8.00000003.01F4.001E.0001.0001.0001.0001.03.0012FFFC..07.01.01.00.0006.01.FF.000080C2.00000000.01.FF.0001D8A8.00000000.01.FF.0009DD8B.00000000.01.FF.00125020.00000000.01.FF.000080C3.00000000.01.FF.0001D4C0.00000000...DA

DACSIM Message Parser

=Message: 00B2.004C.02F8.0002...01.4D3131323330544537383434..01.00000007.30303030303338343235313037313639.238E.0000..02.00000027.00000001.0000019B.00000004.00000000.00000000.00000001..03.00000003.01.06..04.000003FF.000003E8.00000003.01F4.001E.0001.0001.0001.0001.03.0012FFFC..07.01.01.00.0006.01.FF.000080C2.00000000.01.FF.0001D8A8.00000000.01.FF.0009DD8B.00000000.01.FF.00125020.00000000.01.FF.000080C3.00000000.01.FF.0001D4C0.00000000...DA
-HEADER---------------------------------------
 Size:      00B2 (178: Valid)
 Sequence:  004C (76)
 Type:      02F8 (760)
 CheckSum:  DA   (Valid)

-PAYLOAD--------------------------------------
CompType: 07
 Identifier Component:
   BSICode:   0002 (2)
   ReqType:   01 (Add)
   SerialN:   4D3131323330544537383434 (M11230TE7844)
 Type Component (01):
   BitMask:   00000007 (00000007)
   UnitAdd:   30303030303338343235313037313639 (0000038425107169)
   EqType:    238E (9102)
   EqSubType: 0000 (0)
 Plant Component (02):
   BitMask:   00000027 (00000027)
   HeadEnd:   00000001 (1)
   US Plant:  0000019B (411)
   DS Plant:  00000004 (4)
   Reserved1: 00000000 (0)
   Reserved2: 00000000 (0)
   VCMhandle: 00000001 (1)
 State Component (03):
   BitMask:   00000003 (00000003)
   OnPlant:   01 (1)
   Op Code:   06 (6: Activate)
 Feature Component (04):
   BitMask:   000003FF (000003FF)
   Credit:    [In bitmask: 1] 000003E8 (1000)
   Purchases: [In bitmask: 1] 00000003 (3)
   MaxCost:   [In bitmask: 1] 01F4 (500)
   TimeZone:  [In bitmask: 1] 001E (30)
   EPGRegion: [In bitmask: 1] 0001 (1)
   RegionCfg: [In bitmask: 1] 0001 (1)
   TurnOffVC: [In bitmask: 1] 0001 (1)
   TurnOnVC:  [In bitmask: 1] 0001 (1)
   Output CH: [In bitmask: 1] 03 (3)
   FeatSets:  [In bitmask: 1] 0012FFFC (00000000000100101111111111111100)
     + parental_control_channel
     + parental_control_rating
     + volume_mute
     + vcr_tuning
     + last_channel
     + favorite_channel
     + parental_control_time
     + parental_control_cost
     + interactive_ir
     + purchase_cancellation
     + IPPV
     + remote_control
     + time_controlled_programming
     + user_specified_language
     + applications_interface_port
     + nvod_enabled
 Authorization Component (07):
   Clear Pkg: 01 (1)
   Clear Srv: 01 (1)
   Clear Prg: 00 (0)
   Num Recs:  0006 (6)
     0.Flag:    01 (1)
       Type:    FF (255)
       Handle:  000080C2 (32962)
       Program: 00000000 (0)
     1.Flag:    01 (1)
       Type:    FF (255)
       Handle:  0001D8A8 (121000)
       Program: 00000000 (0)
     2.Flag:    01 (1)
       Type:    FF (255)
       Handle:  0009DD8B (646539)
       Program: 00000000 (0)
     3.Flag:    01 (1)
       Type:    FF (255)
       Handle:  00125020 (1200160)
       Program: 00000000 (0)
     4.Flag:    01 (1)
       Type:    FF (255)
       Handle:  000080C3 (32963)
       Program: 00000000 (0)
     5.Flag:    01 (1)
       Type:    FF (255)
       Handle:  0001D4C0 (120000)
       Program: 00000000 (0)

--------------------------------------------------------------------------------
=END OF MESSAGE=================================================================
--------------------------------------------------------------------------------
```

### Get list of existing STBs
```
$ php dacsim.php --show stb
Found 1 SetTopBox:
 - M11230TE7844
```

### Get data of a specific STB
```
$ php dacsim.php --describe-stb M11230TE7844
STB Description

 SerialN:   M11230TE7844
 BSICode:   2
 Instaled:  26.08.2015 14:17:21
 UnitAdd:   0000038425107169 | EqType: 9102 | EqSubType: 0
 HeadEnd:   1 | US Plant: 411 | DS Plant: 4 | VCMhandle: 1
 OnPlant:   1
 Credit:    1000 | Purchases: 3 | MaxCost: 500
 TimeZone:  30 | EPGRegion: 1 | RegionCfg: 1
 TurnOnVC:  1 | TurnOffVC: 1 | Output CH: 3
 FeatSets:  1245180 (00000000000100101111111111111100)
 Services:  32962, 32963, 120000, 121000, 646539, 1200160
 Programs:
```

### Get current topology:
```
$ php dacsim.php --show-all
Found 2 BSI:
- 1
- 2
Found 1 EquipType:
- 9102
Found 1 HeadEnd:
- 1
Found 1 USPlant:
- 411
Found 1 DSPlant:
- 4
Found 1 ChannelMap:
- 1
Found 6 Service:
- 32962
- 32963
- 120000
- 121000
- 646539
- 1200160
Found 1 RegionConfig:
- 1
```

### Get topology items of a specific type:
```
$ php dacsim.php --show headend
Found 1 HeadEnd:
 - 1
```
Available types: bsi, stb, equiptype, headend, usplant, dsplant, channelmap, service, regionconf

### Create a topology item
```
$ php dacsim.php --create bsi 3
bsi created successfuly

$ php dacsim.php --show bsi
Found 3 BSI:
 - 1
 - 2
 - 3
```
Available types: bsi, stb, equiptype, headend, usplant, dsplant, channelmap, service, regionconf

### Delete a topology item
```
$ php dacsim.php --delete bsi 3
bsi deleted successfuly
$ php dacsim.php --show bsi
Found 2 BSI:
 - 1
 - 2
```
Available types: bsi, stb, equiptype, headend, usplant, dsplant, channelmap, service, regionconf

### Generate a checksum for the payload of a command
````
$ php dacsim.php --checksum 004C.02F8.0002...03.4D3131323330544537383434
DACSIM Checksum Generator

=Size:     0015 (21)
=Checksum: EC
=Message:  0015004C02F80002034D3131323330544537383434EC
````

The supplied HEX command MUST not include the length (first 4 nibbles) and the checksum (last 2 nibbles).
```
See that the supplied command is: ----004C02F80002034D3131323330544537383434--
And the response is:              0015004C02F80002034D3131323330544537383434EC
```

The simulator won't accept the first one, only the one with the lenght and the valir checksum.