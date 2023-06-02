rm -rf DashVideo

./ffmpeg -hide_banner -nostats -ignore_unknown -fflags +discardcorrupt -analyzeduration 600000000 -probesize 600000000 -i test.mp4 -threads 4 -preset fast -c:v libx264 -crf 23 -profile:v main -level 3.0 -maxrate:v 1500k -bufsize:v 3000k -pix_fmt yuv420p -r 24 -refs 3 -bf 3 -g 48 -keyint_min 24 -b_strategy 1 -flags +cgop -sc_threshold 0 -movflags negative_cts_offsets+faststart -vf "drawtext=fontfile=Roboto-Regular.ttf:fontcolor=White:fontsize=38:alpha=0.5:box=1:boxborderw=4:boxcolor=black:x=(w-text_w)/2:y=text_h-line_h+60:text='H264 1280x720 1500k 8s \ ':timecode='00\:00\:00\:00':rate=24,drawtext=fontfile=Roboto-Regular.ttf:fontcolor=White:fontsize=38:alpha=0.5:box=1:boxborderw=4:boxcolor=black:x=(w-text_w)/2:y=text_h-line_h+98:text='24fps 48gop frame\:\ %{frame_num}':start_number=1,scale=1280x720:out_range=tv:out_color_matrix=bt709:flags=full_chroma_int+accurate_rnd,format=yuv420p,setsar=1/1" -color_range tv -colorspace bt709 -color_primaries bt709 -color_trc bt709 -an -sn -y TempVideo/temp_720p.mp4

./ffmpeg -hide_banner -nostats -ignore_unknown -fflags +discardcorrupt -analyzeduration 600000000 -probesize 600000000 -i test.mp4 -threads 4 -preset fast -c:v libx264 -crf 23 -profile:v main -level 3.0 -maxrate:v 2000k -bufsize:v 4000k -pix_fmt yuv420p -r 24 -refs 3 -bf 3 -g 48 -keyint_min 24 -b_strategy 1 -flags +cgop -sc_threshold 0 -movflags negative_cts_offsets+faststart -vf "drawtext=fontfile=Roboto-Regular.ttf:fontcolor=White:fontsize=38:alpha=0.5:box=1:boxborderw=4:boxcolor=black:x=(w-text_w)/2:y=text_h-line_h+60:text='H264 1920x1080 2000k 8s \ ':timecode='00\:00\:00\:00':rate=24,drawtext=fontfile=Roboto-Regular.ttf:fontcolor=White:fontsize=38:alpha=0.5:box=1:boxborderw=4:boxcolor=black:x=(w-text_w)/2:y=text_h-line_h+98:text='24fps 48gop frame\:\ %{frame_num}':start_number=1,scale=1920x1080:out_range=tv:out_color_matrix=bt709:flags=full_chroma_int+accurate_rnd,format=yuv420p,setsar=1/1" -color_range tv -colorspace bt709 -color_primaries bt709 -color_trc bt709 -an -sn -y TempVideo/temp_1080p.mp4



./ffmpeg -ss 00:00:00 -t 00:01:00 -i TempVideo/temp_720p.mp4 -vcodec copy -acodec copy -y TempVideo/part1_temp_720p.mp4

./ffmpeg -ss 00:01:00 -t 00:02:00 -i TempVideo/temp_720p.mp4 -vcodec copy -acodec copy -y TempVideo/part2_temp_720p.mp4

./ffmpeg -ss 00:00:00 -t 00:01:00 -i TempVideo/temp_1080p.mp4 -vcodec copy -acodec copy -y TempVideo/part1_temp_1080p.mp4

./ffmpeg -ss 00:01:00 -t 00:02:00 -i TempVideo/temp_1080p.mp4 -vcodec copy -acodec copy -y TempVideo/part2_temp_1080p.mp4

./MP4Box -crypt gpacdrm_key1.xml TempVideo/part1_temp_720p.mp4 -out TempVideo/cenc_part1_temp_720p.mp4

./MP4Box -crypt gpacdrm_key2.xml TempVideo/part2_temp_720p.mp4 -out TempVideo/cenc_part2_temp_720p.mp4

./MP4Box -crypt gpacdrm_key1.xml TempVideo/part1_temp_1080p.mp4 -out TempVideo/cenc_part1_temp_1080p.mp4

./MP4Box -crypt gpacdrm_key2.xml TempVideo/part2_temp_1080p.mp4 -out TempVideo/cenc_part2_temp_1080p.mp4

./MP4Box -dash 8000 -frag 2666 -dash-scale 1000 -mem-frags -rap -profile dashavc264:live -profile-ext urn:hbbtv:dash:profile:isoff-live:2012 -min-buffer 2000 -mpd-title refapp -mpd-info-url http://refapp -bs-switching no -sample-groups-traf -single-traf --tfdt64 --tfdt_traf --noroll=yes --btrt=no --truns_first=yes --cmaf=cmf2 -subsegs-per-sidx 2 -segment-name  \$RepresentationID\$/\$Number\$\$Init=init\$_\$Period\$ -out DashVideo/Video1/manifest.mpd TempVideo/cenc_part1_temp_720p.mp4:#trackID=1:id=720p:period=p0:asID=1:role=main TempVideo/cenc_part1_temp_1080p.mp4:#trackID=1:id=1080p:period=p0:asID=1:role=main 

./MP4Box -dash 8000 -frag 2666 -dash-scale 1000 -mem-frags -rap -profile dashavc264:live -profile-ext urn:hbbtv:dash:profile:isoff-live:2012 -min-buffer 2000 -mpd-title refapp -mpd-info-url http://refapp -bs-switching no -sample-groups-traf -single-traf --tfdt64 --tfdt_traf --noroll=yes --btrt=no --truns_first=yes --cmaf=cmf2 -subsegs-per-sidx 2 -segment-name  \$RepresentationID\$/\$Number\$\$Init=init\$_\$Period\$ -out DashVideo/Video2/manifest.mpd TempVideo/cenc_part2_temp_720p.mp4:#trackID=1:id=720p:period=p0:asID=1:role=main TempVideo/cenc_part2_temp_1080p.mp4:#trackID=1:id=1080p:period=p0:asID=1:role=main 



./MP4Box -dash 8000 -frag 2666 -dash-scale 1000 -mem-frags -rap -profile dashavc264:live -profile-ext urn:hbbtv:dash:profile:isoff-live:2012 -min-buffer 2000 -mpd-title refapp -mpd-info-url http://refapp -bs-switching no -sample-groups-traf -single-traf --tfdt64 --tfdt_traf --noroll=yes --btrt=no --truns_first=yes --cmaf=cmf2 -subsegs-per-sidx 2 -segment-name \$RepresentationID\$/\$Number\$\$Init=init\$_\$Period\$ -out DashVideo/Both/manifest.mpd TempVideo/cenc_part1_temp_720p.mp4:#trackID=1:id=720p:period=p0:asID=1:role=main TempVideo/cenc_part1_temp_1080p.mp4:#trackID=1:id=1080p:period=p0:asID=1:role=main TempVideo/cenc_part2_temp_720p.mp4:#trackID=1:id=720p:period=p1:asID=1:role=main TempVideo/cenc_part2_temp_1080p.mp4:#trackID=1:id=1080p:period=p1:asID=1:role=main
